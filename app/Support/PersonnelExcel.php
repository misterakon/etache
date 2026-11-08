<?php

namespace App\Support;

use App\Models\Personnel;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Format Excel du personnel : modèle à remplir, export des agents, lecture d'un fichier à importer. Colonnes reconnues par leur titre. */
class PersonnelExcel
{
    /** clé => [titre, obligatoire, largeur, est une date, exemple] */
    public const COLONNES = [
        'matricule' => ['Matricule', true, 14, false, 'EXEMPLE-000'],
        'nom' => ['Nom', true, 18, false, 'KOUASSI'],
        'prenom' => ['Prénom', true, 18, false, 'Aya Marie'],
        'civilite' => ['Civilité', false, 11, false, 'Mme'],
        'genre' => ['Genre', false, 12, false, 'Féminin'],
        'date_naissance' => ['Date de naissance', false, 16, true, '14/03/1985'],
        'lieu_naissance' => ['Lieu de naissance', false, 18, false, 'Abidjan'],
        'statut_matrimonial' => ['Statut matrimonial', false, 18, false, 'Mariée'],
        'nombre_enfants' => ["Nombre d'enfants", false, 12, false, '2'],
        'nom_pere' => ['Nom du père', false, 20, false, null],
        'nom_mere' => ['Nom de la mère', false, 20, false, null],
        'email' => ['E-mail', false, 28, false, 'aya.kouassi@dgepn.ci'],
        'telephone' => ['Téléphone 1', false, 16, false, '+225 07 00 00 00 00'],
        'telephone_2' => ['Téléphone 2', false, 16, false, null],
        'lieu_habitation' => ["Lieu d'habitation", false, 18, false, 'Cocody'],
        'direction' => ['Direction', false, 26, false, "Direction de l'Informatique"],
        'sous_direction' => ['Sous-direction', false, 24, false, null],
        'service' => ['Service', false, 24, false, 'Service Réseaux'],
        'fonction' => ['Fonction', false, 24, false, "Chargée d'études"],
        'type_agent' => ["Type d'agent", false, 16, false, 'Fonctionnaire'],
        'emploi' => ['Emploi', false, 24, false, null],
        'grade' => ['Grade', false, 10, false, null],
        'classe' => ['Classe', false, 10, false, null],
        'echelon' => ['Échelon', false, 10, false, null],
        'date_premiere_prise_service' => ['1re prise de service', false, 18, true, '02/01/2012'],
        'date_prise_service' => ['Prise de service', false, 16, true, '15/06/2019'],
        'date_depart_retraite' => ['Départ à la retraite', false, 18, true, null],
        'creer_compte' => ['Créer un compte', false, 14, false, 'Oui'],
    ];

    private const SYNONYMES = [
        'prenom' => ['prenoms'], 'email' => ['e mail', 'mail', 'courriel'], 'telephone' => ['telephone', 'tel', 'tel 1', 'telephone 1'],
        'telephone_2' => ['tel 2', 'telephone 2'], 'lieu_habitation' => ['habitation', 'adresse'], 'date_premiere_prise_service' => ['premiere prise de service', '1ere prise de service'],
        'date_prise_service' => ['date de prise de service'], 'date_depart_retraite' => ['depart retraite', 'retraite', 'depart a la retraite'],
        'creer_compte' => ['compte', 'creer un compte utilisateur'], 'nombre_enfants' => ['enfants', 'nb enfants'], 'type_agent' => ['type agent', 'type d agent'],
    ];

    public static function norme(?string $texte): string
    {
        return DoublonsPersonnel::norme(str_replace('*', '', (string) $texte));
    }

    /** @return list<string> titres d'en-tête */
    public static function entetes(): array
    {
        return array_values(array_map(fn ($c) => $c[0], self::COLONNES));
    }

    // ------------------------------------------------------------------ modèle
    public static function modele(): StreamedResponse
    {
        $classeur = new Spreadsheet;
        $lm = $classeur->getActiveSheet();
        $lm->setTitle('Lisez-moi');
        $lm->getColumnDimension('A')->setWidth(34);
        $lm->getColumnDimension('B')->setWidth(95);
        $lm->setCellValue('A1', "Modèle d'import du personnel : e-Tâche");
        $lm->getStyle('A1')->getFont()->setBold(true)->setSize(15);
        $regles = [
            ['En-tête bleu foncé *', 'Colonne OBLIGATOIRE : Matricule, Nom, Prénom.'],
            ['En-tête bleu clair', "Colonne facultative. Une cellule vide est ignorée : aucune valeur existante n'est effacée."],
            ['Ligne grise (ligne 2)', "Exemple de saisie : à laisser telle quelle. L'import ignore toute ligne dont le matricule commence par « EXEMPLE »."],
            ['Matricule', "Unique. S'il existe déjà dans l'application, la fiche est MISE À JOUR ; sinon elle est CRÉÉE."],
            ['Dates', 'Format jj/mm/aaaa (ex. 14/03/1985).'],
            ['Direction, service, emploi, grade…', "Écrire le nom exact, comme dans l'application. Une valeur inconnue est signalée à la prévisualisation, jamais créée en silence."],
            ['Créer un compte', "« Oui » : un compte au profil Agent est créé avec l'e-mail ; un mot de passe provisoire est généré."],
            ['Tout ou rien', "Si une ligne contient une erreur, rien n'est importé : la prévisualisation liste chaque erreur avec son numéro de ligne."],
            ['Confidentialité', "Ce fichier contient des données personnelles : supprimez-le après l'import."],
        ];
        foreach ($regles as $i => [$a, $b]) {
            $lm->setCellValue('A'.($i + 3), $a);
            $lm->setCellValue('B'.($i + 3), $b);
            $lm->getStyle('A'.($i + 3))->getFont()->setBold(true);
            $lm->getStyle('B'.($i + 3))->getAlignment()->setWrapText(true);
        }

        $f = $classeur->createSheet();
        $f->setTitle('Personnel');
        $i = 1;
        foreach (self::COLONNES as [$titre, $oblig, $largeur, $date, $exemple]) {
            $col = Coordinate::stringFromColumnIndex($i);
            $f->setCellValue($col.'1', $titre.($oblig ? ' *' : ''));
            $f->setCellValueExplicit($col.'2', (string) $exemple, DataType::TYPE_STRING);
            $f->getColumnDimension($col)->setWidth($largeur);
            $f->getStyle($col.'1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $oblig ? '1F4E79' : '2B6CB0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $f->getStyle($col.'2')->applyFromArray(['font' => ['italic' => true, 'color' => ['rgb' => '7A7A7A']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF1F5']]]);
            $f->getStyle($col.'3:'.$col.'500')->getNumberFormat()->setFormatCode('@'); // matricules, téléphones et dates saisis en texte
            $i++;
        }
        $f->getRowDimension(1)->setRowHeight(32);
        $f->freezePane('D2');
        $classeur->setActiveSheetIndex(1);

        return response()->streamDownload(fn () => (new Xlsx($classeur))->save('php://output'), 'modele_import_personnel.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    // ------------------------------------------------------------------ export
    /** @return list<list<mixed>> */
    public static function lignes(Collection $agents): array
    {
        $date = fn ($d) => $d?->format('d/m/Y');

        return $agents->map(function (Personnel $a) use ($date) {
            $r = $a->rattachement();

            return [
                $a->matricule, $a->nom, $a->prenom, $a->civilite?->libelle, $a->genre?->libelle, $date($a->date_naissance), $a->lieu_naissance,
                $a->statutMatrimonial?->libelle, $a->nombre_enfants, $a->nom_pere, $a->nom_mere, $a->email, $a->telephone, $a->telephone_2, $a->lieu_habitation,
                $r['direction']?->nom, $r['sous_direction']?->nom, $r['service']?->nom, $a->fonction, $a->typeAgent?->libelle, $a->emploi?->libelle,
                $a->grade?->code, $a->classeAgent?->libelle, $a->echelon?->numero, $date($a->date_premiere_prise_service), $date($a->date_prise_service), $date($a->date_depart_retraite),
                $a->user ? 'Oui' : '',
            ];
        })->all();
    }

    // ------------------------------------------------------------------ lecture
    /** @return array{lignes: list<array{num: int, valeurs: array<string, ?string>}>, erreurs: list<string>} */
    public static function lire(string $chemin): array
    {
        try {
            $reader = IOFactory::createReader('Xlsx');
            if (! $reader->canRead($chemin)) {
                throw new \RuntimeException('format');
            }
            $noms = $reader->listWorksheetNames($chemin);
            $nom = collect($noms)->first(fn ($n) => self::norme($n) === 'personnel')
                ?? collect($noms)->first(fn ($n) => self::norme($n) !== 'lisez moi')
                ?? $noms[0];
            $reader->setLoadSheetsOnly([$nom]);
            $feuille = $reader->load($chemin)->getSheetByName($nom) ?? throw new \RuntimeException('onglet');
        } catch (\Throwable $e) {
            return ['lignes' => [], 'erreurs' => ["Fichier illisible : ce n'est pas un classeur Excel valide (.xlsx)."]];
        }

        $cartes = [];
        foreach (self::COLONNES as $cle => [$titre]) {
            $cartes[self::norme($titre)] = $cle;
            foreach (self::SYNONYMES[$cle] ?? [] as $s) {
                $cartes[self::norme($s)] = $cle;
            }
        }

        $maxLigne = min($feuille->getHighestDataRow(), 1000);
        $maxCol = Coordinate::columnIndexFromString($feuille->getHighestDataColumn());

        $ligneEntete = null;
        $colonnes = [];
        for ($r = 1; $r <= min(10, $maxLigne) && $ligneEntete === null; $r++) {
            $trouvees = [];
            for ($c = 1; $c <= $maxCol; $c++) {
                $cle = $cartes[self::norme((string) self::texte($feuille->getCell(Coordinate::stringFromColumnIndex($c).$r)->getValue()))] ?? null;
                if ($cle && ! isset($trouvees[$cle])) {
                    $trouvees[$cle] = $c;
                }
            }
            if (isset($trouvees['matricule'], $trouvees['nom'])) {
                $ligneEntete = $r;
                $colonnes = $trouvees;
            }
        }
        if ($ligneEntete === null) {
            return ['lignes' => [], 'erreurs' => ['En-têtes introuvables : le fichier doit comporter au moins les colonnes « Matricule » et « Nom » (utilisez le modèle).']];
        }

        $lignes = [];
        for ($r = $ligneEntete + 1; $r <= $maxLigne; $r++) {
            $valeurs = [];
            $vide = true;
            foreach ($colonnes as $cle => $c) {
                $v = self::valeur($feuille->getCell(Coordinate::stringFromColumnIndex($c).$r), self::COLONNES[$cle][3]);
                $valeurs[$cle] = $v;
                $vide = $vide && $v === null;
            }
            if (! $vide) {
                $lignes[] = ['num' => $r, 'valeurs' => $valeurs];
            }
        }

        return ['lignes' => $lignes, 'erreurs' => []];
    }

    private static function texte($v): string
    {
        return $v instanceof RichText ? $v->getPlainText() : (string) $v;
    }

    /** Valeur nettoyée ; une date illisible renvoie « !texte » pour être signalée. */
    private static function valeur($cellule, bool $estDate): ?string
    {
        $v = $cellule->getValue();
        if ($v instanceof RichText) {
            $v = $v->getPlainText();
        }
        if ($v === null || trim((string) $v) === '') {
            return null;
        }
        if ($estDate) {
            if (is_numeric($v)) {
                try {
                    return Date::excelToDateTimeObject((float) $v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return '!'.$v;
                }
            }
            $s = trim((string) $v);
            foreach (['!j/n/Y', '!j-n-Y', '!j.n.Y', '!Y-m-d'] as $format) {
                $d = \DateTime::createFromFormat($format, $s);
                $erreurs = \DateTime::getLastErrors();
                if ($d && ($erreurs === false || ($erreurs['warning_count'] === 0 && $erreurs['error_count'] === 0))) {
                    return $d->format('Y-m-d');
                }
            }

            return '!'.$s;
        }
        if (is_float($v) && floor($v) == $v) {
            $v = (string) (int) $v;
        }

        return trim((string) $v);
    }
}
