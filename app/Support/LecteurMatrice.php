<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Lecture du fichier Excel d'import. Les colonnes sont reconnues par leur TITRE (pas par leur position) :
 *  - l'ordre des colonnes n'a pas d'importance ;
 *  - les colonnes inconnues sont ignorées (signalées) ;
 *  - les colonnes facultatives peuvent être absentes ; les colonnes obligatoires doivent être présentes ;
 *  - quelques synonymes sont acceptés (« Échéance » pour « Date limite »…) ;
 *  - la ligne d'en-têtes est cherchée dans les 10 premières lignes de l'onglet.
 * Seul l'onglet « Matrice » est lu (ou l'unique onglet du fichier).
 *
 * Les cellules fusionnées n'ont de valeur que dans leur cellule d'ancrage : les autres sont lues vides
 * (le report de la valeur est fait par ImportMatrice::analyser()).
 */
class LecteurMatrice
{
    private const LIGNES_RECHERCHE_ENTETES = 10;

    /** Synonymes acceptés (titres sans accents, ni parenthèses, ni astérisque), en plus du titre du modèle. */
    private const SYNONYMES = [
        'resultat' => ['resultat'],
        'ac_code' => ['code ac', 'code de l action cle', 'code action'],
        'dir_porteuse' => ['direction principale', 'direction porteuse'],
        'dir_associees' => ['direction associee', 'directions associees'],
        'am' => ['action mesurable a executer'],
        'tache' => ['tache afferente'],
        'indicateur' => ['indicateur de suivi'],
        'livrable' => ['livrable attendu'],
        'responsable' => ['responsable de l execution', 'responsable execution'],
        'pilote' => ['pilote coordination'],
        'contributeurs' => ['contributeur'],
        'date_debut' => ['debut prevu', 'date de debut', 'debut'],
        'date_fin' => ['date fin', 'fin prevue', 'date de fin', 'echeance'],
        'statut' => ['statut rag'],
        'budget' => ['budget'],
        'observations' => ['observation', 'observations risques'],
    ];

    /**
     * @return array{lignes: array, erreurs: array<int, string>, avertissements: array<int, string>}
     */
    public static function lire(string $chemin, ?callable $progression = null): array
    {
        if ($progression) {
            $progression('lecture', 0, 0); // chargement du classeur : durée inconnue
        }

        try {
            $reader = IOFactory::createReader('Xlsx'); // uniquement .xlsx : pas de détection de CSV ou HTML
            if (! $reader->canRead($chemin)) {
                throw new \RuntimeException('format');
            }

            // Seul l'onglet « Matrice » est lu : les autres onglets du fichier (mode d'emploi, exemple, plan source…) sont ignorés.
            $noms = $reader->listWorksheetNames($chemin);
            $nom = self::choisirOnglet($noms);
            if ($nom === null) {
                $liste = implode(', ', array_map(fn ($n) => "« {$n} »", array_slice($noms, 0, 6)));

                return self::echec(["Onglet « Matrice » introuvable. Le fichier contient plusieurs onglets ({$liste}) : renommez celui à importer « Matrice »."]);
            }

            $reader->setReadDataOnly(false);
            $reader->setLoadSheetsOnly([$nom]);
            $classeur = $reader->load($chemin);
        } catch (\Throwable $e) {
            return self::echec(["Fichier illisible : ce n'est pas un classeur Excel valide (.xlsx)."]);
        }

        $feuille = $classeur->getSheetByName($nom) ?? $classeur->getSheet(0);

        $entetes = self::reconnaitreEntetes($feuille);
        if ($entetes['erreurs']) {
            return self::echec($entetes['erreurs']);
        }

        $colonnes = $entetes['colonnes']; // clé => index de colonne
        $derniere = $feuille->getHighestDataRow();
        $premiere = $entetes['ligne'] + 1;
        if ($derniere - $entetes['ligne'] > ImportMatrice::MAX_LIGNES) {
            return self::echec(['Le fichier dépasse '.ImportMatrice::MAX_LIGNES.' lignes : découpez-le en plusieurs imports.']);
        }

        $cles = array_column(ImportMatrice::COLONNES, 0);
        $lignes = [];
        $total = max(1, $derniere - $premiere + 1);
        $pas = max(1, (int) floor($total / 40));
        for ($r = $premiere; $r <= $derniere; $r++) {
            if ($progression && ($r === $derniere || ($r - $premiere) % $pas === 0)) {
                $progression('lecture', $r - $premiere + 1, $total);
            }

            $valeurs = [];
            $vide = true;
            foreach ($cles as $cle) {
                $v = isset($colonnes[$cle]) ? self::valeur($feuille, $colonnes[$cle], $r) : null;
                $valeurs[] = $v;
                if ($v !== null && $v !== '') {
                    $vide = false;
                }
            }
            if (! $vide) {
                $lignes[] = ['ligne' => $r, 'v' => $valeurs];
            }
        }

        if (! $lignes) {
            return self::echec(["Aucune ligne à importer sous les en-têtes de l'onglet « {$nom} »."]);
        }

        return ['lignes' => $lignes, 'erreurs' => [], 'avertissements' => $entetes['avertissements']];
    }

    private static function echec(array $erreurs): array
    {
        return ['lignes' => [], 'erreurs' => $erreurs, 'avertissements' => []];
    }

    /** Onglet à lire : « Matrice » (quelle que soit sa position), ou l'unique onglet du fichier ; sinon null. */
    private static function choisirOnglet(array $noms): ?string
    {
        foreach ($noms as $nom) {
            if (ImportMatrice::norm($nom) === 'matrice') {
                return $nom;
            }
        }

        return count($noms) === 1 ? $noms[0] : null;
    }

    /** Titre d'en-tête comparable : sans accents, sans parenthèses, sans astérisque, sans ponctuation. */
    private static function cleEntete($texte): string
    {
        $t = (string) ImportMatrice::texte($texte);
        $t = (string) preg_replace('/\([^)]*\)/u', ' ', $t);
        $t = ImportMatrice::norm($t);

        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', $t));
    }

    /** Table « titre comparable => clé de colonne », avec les titres du modèle et les synonymes. */
    private static function table(): array
    {
        $table = [];
        foreach (ImportMatrice::COLONNES as [$cle, $titre]) {
            $table[self::cleEntete($titre)] = $cle;
        }
        foreach (self::SYNONYMES as $cle => $liste) {
            foreach ($liste as $s) {
                $table[$s] = $cle;
            }
        }

        return $table;
    }

    /**
     * Cherche la ligne d'en-têtes (celle qui en reconnaît le plus) et associe chaque clé à sa colonne.
     *
     * @return array{ligne: int, colonnes: array<string, int>, erreurs: array, avertissements: array}
     */
    private static function reconnaitreEntetes(Worksheet $feuille): array
    {
        $table = self::table();
        $nbColonnes = min(60, Coordinate::columnIndexFromString($feuille->getHighestDataColumn()));
        $limite = min(self::LIGNES_RECHERCHE_ENTETES, max(1, $feuille->getHighestDataRow()));

        $meilleure = ['ligne' => 0, 'nb' => 0];
        for ($r = 1; $r <= $limite; $r++) {
            $nb = 0;
            for ($c = 1; $c <= $nbColonnes; $c++) {
                if (isset($table[self::cleEntete(self::valeur($feuille, $c, $r))])) {
                    $nb++;
                }
            }
            if ($nb > $meilleure['nb']) {
                $meilleure = ['ligne' => $r, 'nb' => $nb];
            }
        }

        if ($meilleure['nb'] < 3) {
            return ['ligne' => 0, 'colonnes' => [], 'avertissements' => [], 'erreurs' => [
                "Les en-têtes de colonnes n'ont pas été reconnus dans l'onglet « Matrice ». "
                .'Téléchargez le modèle depuis cette page et recopiez vos données dedans.',
            ]];
        }

        $colonnes = [];
        $ignorees = [];
        $doublons = [];
        for ($c = 1; $c <= $nbColonnes; $c++) {
            $brut = ImportMatrice::texte(self::valeur($feuille, $c, $meilleure['ligne']));
            if ($brut === null) {
                continue;
            }
            $cle = $table[self::cleEntete($brut)] ?? null;
            if ($cle === null) {
                $ignorees[] = $brut;
            } elseif (isset($colonnes[$cle])) {
                $doublons[] = $brut;
            } else {
                $colonnes[$cle] = $c;
            }
        }

        $manquantes = [];
        foreach (ImportMatrice::COLONNES as [$cle, $titre, , $obligatoire]) {
            if ($obligatoire && ! isset($colonnes[$cle])) {
                $manquantes[] = "« {$titre} »";
            }
        }
        if ($manquantes) {
            return ['ligne' => 0, 'colonnes' => [], 'avertissements' => [], 'erreurs' => [
                'Colonnes obligatoires manquantes : '.implode(', ', $manquantes).'. '
                .'Ajoutez-les (même ordre ou non) avec ces titres, ou utilisez le modèle téléchargé depuis cette page.',
            ]];
        }

        $avertissements = [];
        if ($ignorees) {
            $avertissements[] = 'Colonnes non reconnues, ignorées : '.implode(', ', array_map(fn ($t) => "« {$t} »", array_slice($ignorees, 0, 8))).(count($ignorees) > 8 ? ', …' : '').'.';
        }
        if ($doublons) {
            $avertissements[] = 'Colonnes présentes plusieurs fois (la première est utilisée) : '.implode(', ', array_map(fn ($t) => "« {$t} »", array_slice($doublons, 0, 8))).'.';
        }
        if ($meilleure['ligne'] > 1) {
            $avertissements[] = "Les en-têtes ont été trouvés à la ligne {$meilleure['ligne']} : les lignes au-dessus sont ignorées.";
        }

        return ['ligne' => $meilleure['ligne'], 'colonnes' => $colonnes, 'erreurs' => [], 'avertissements' => $avertissements];
    }

    private static function valeur(Worksheet $feuille, int $colonne, int $ligne)
    {
        if (! $feuille->cellExists([$colonne, $ligne])) {
            return null;
        }

        $cellule = $feuille->getCell([$colonne, $ligne]);
        try {
            $v = $cellule->isFormula() ? $cellule->getCalculatedValue() : $cellule->getValue();
        } catch (\Throwable $e) {
            return null;
        }

        if ($v instanceof RichText) {
            $v = $v->getPlainText();
        }
        if ((is_int($v) || is_float($v)) && Date::isDateTime($cellule)) {
            return Date::excelToDateTimeObject($v);
        }

        return is_string($v) ? trim($v) : $v;
    }
}
