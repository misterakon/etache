<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Modèle Excel d'import de la matrice : 4 onglets (Mode d'emploi, Matrice, Exemple, Listes).
 * L'onglet Listes contient les codes de direction, statuts et priorités réels de l'application.
 */
class ModeleImportMatrice
{
    private const BLEU_FONCE = '1F3864';

    private const BLEU_CLAIR = '2F75B5';

    private const GRIS = 'F2F2F2';

    private const ROUGE = 'C00000';

    private const DERNIERE_LIGNE = 500;

    private const EXEMPLE = [
        'axe' => 'Axe 1 — Harmonisation du cadre juridique et de la gouvernance des organes des EPN / EPH / EPAST /SA',
        'resultat' => 'Cadre juridique et statutaire des organes de gouvernance des EPN/EPH modernisé et sécurisé',
        'ac_code' => 'R07',
        'ac' => 'Révision du cadre législatif et réglementaire applicable aux EPN et EPH',
        'taches' => [
            ['Réaliser l\'analyse comparative des pratiques régionales et internationales (benchmarking) et concerter les administrations concernées', 'Recenser exhaustivement les textes législatifs et réglementaires en vigueur applicables aux EPN/EPH et identifier les marges d\'amélioration', 'Nombre de textes recensés ; taux de couverture du recensement (100%)', 'Note d\'analyse des insuffisances des textes soumises au Directeur Général', 'MAT-001', 'MAT-002', 'agent.exemple@exemple.ci; MAT-003', '2026-01-15', '2026-03-30', 'Haute', 'En cours', 40, 2500000, "Exemple d'observation"],
            ['Réaliser l\'analyse comparative des pratiques régionales et internationales (benchmarking) et concerter les administrations concernées', 'Consolider une matrice des marges d\'amélioration par thématique', 'Matrice des marges d’amélioration élaborée et consolidée (Oui/Non)', 'Note de transmission de la Matrice des marges d\'amélioration par thématique, au Directeur Général', 'agent.exemple@exemple.ci', null, null, null, '2026-04-15', null, null, null, null, null],
            ['Réaliser l\'analyse comparative des pratiques régionales et internationales (benchmarking) et concerter les administrations concernées', 'Collecter et analyser les cadres juridiques applicables aux opérateurs publics dans ces juridictions', 'Nombre de juridictions collectées et analysées  nombre de cadres juridiques comparés', 'Note d\'analyse comparative (benchmarking)', 'MAT-002', null, 'MAT-003', null, '2026-09-15', 'Moyenne', 'Non démarrée', 0, null, null],
            ['Établir le diagnostic du cadre législatif et réglementaire applicable aux EPN ,EPH, EPAST et SA', 'Rédiger le rapport de diagnostic consolidant le recensement et le benchmarking', 'Rapport de diagnostic produit et validé (Oui/Non)', 'Rapport de diagnostic du cadre législatif et réglementaire', 'MAT-001', null, null, null, '2026-09-30', null, null, null, null, null],
        ],
    ];

    private const MODE_EMPLOI = [
        ['PRINCIPE', null],
        ['Une ligne = une tâche', "Chaque ligne de l'onglet « Matrice » décrit une tâche, rattachée à son axe, son action clé et son action mesurable."],
        ['Deux présentations acceptées', '1) Compact : Axe, Résultat attendu, Action clé… écrits une seule fois (cellules fusionnées ou vides en dessous, comme dans votre plan actuel). 2) Complet : toutes les valeurs répétées sur chaque ligne de tâche. Les deux se valident de la même façon.'],
        ['Valeurs répétées identiques', "Quand une valeur est répétée, elle doit être identique d'une ligne à l'autre (même libellé d'axe, même code et même libellé d'action clé). Une incohérence est signalée à la prévisualisation."],
        ['Exercice', "Il n'est pas dans le fichier : vous le choisissez à l'écran d'import (un exercice clos est refusé)."],
        ['COLONNES OBLIGATOIRES (en-tête bleu foncé, *)', null],
        ['Structure', 'Axe, Résultat attendu, Code action clé (ex. R06), Action clé, Action mesurable, Tâche.'],
        ['Contenu', 'Indicateur, Livrable.'],
        ['Responsable et échéance', "Responsable (matricule ou email de l'agent), Date limite (JJ/MM/AAAA)."],
        ['COLONNES FACULTATIVES (en-tête bleu clair)', null],
        ['Directions', "Direction porteuse (un code) et Directions associées (codes séparés par un point-virgule) : peuvent être vides. Si un code est renseigné, il doit exister (colonne « Code » de la page Directions de l'application)."],
        ['Pilote, Contributeurs', "Peuvent être vides. Matricule ou email de l'agent ; plusieurs contributeurs séparés par un point-virgule."],
        ['Date début', 'Peut être vide. Si renseignée, elle doit être antérieure ou égale à la date limite.'],
        ['Priorité', 'Peut être vide (priorité non définie). Valeurs de l\'onglet « Listes ».'],
        ['Statut, Avancement (%)', 'Peuvent être vides : statut « Non démarrée » et avancement 0 %. « Terminée » passe l\'avancement à 100 %. Avancement : entier de 0 à 100.'],
        ['Budget estimé, Observations', 'Peuvent être vides.'],
        ['AGENTS ET DIRECTIONS', null],
        ['Matricule ou email uniquement', "Le nom n'est pas accepté. Le matricule est le plus sûr : un email partagé par plusieurs agents est refusé (utilisez alors le matricule). Un agent sans matricule ni email ne peut pas être cité."],
        ['Codes des directions', "Le code est celui saisi dans la page Directions. Une direction sans code ne peut pas être citée : renseignez-le d'abord."],
        ['Codes automatiques', "Les codes des axes, actions mesurables et tâches sont générés par l'application. Seul le code de l'action clé est saisi."],
        ["VÉRIFICATION À L'IMPORT", null],
        ['Prévisualisation', "Avant d'importer, l'écran affiche chaque ligne avec ses erreurs (champ obligatoire vide, agent ou direction introuvable, date incohérente…)."],
        ['Tout ou rien', "Une seule erreur bloque l'import : rien n'est enregistré tant que le fichier n'est pas corrigé."],
        ['Doublons', "Un élément déjà présent dans l'exercice (même libellé au même endroit) n'est pas recréé, et les éléments existants ne sont jamais modifiés."],
    ];

    /**
     * @param  array<int, array{code: string, nom: string}>  $directions  directions ayant un code
     * @param  array<int, string>  $statuts  libellés des statuts actifs
     * @param  array<int, string>  $priorites  libellés des priorités actives
     */
    public static function generer(array $directions, array $statuts, array $priorites): Spreadsheet
    {
        $classeur = new Spreadsheet;
        $classeur->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        self::modeEmploi($classeur->getActiveSheet());
        self::matrice($classeur->createSheet(), count($statuts), count($priorites));
        self::exemple($classeur->createSheet(), $directions[0]['code'] ?? 'CODE-DIR-1', $directions[1]['code'] ?? ($directions[0]['code'] ?? 'CODE-DIR-2'));
        self::listes($classeur->createSheet(), $directions, $statuts, $priorites);

        $classeur->setActiveSheetIndex(0);

        return $classeur;
    }

    /* ------------------------------------------------------------------ */

    private static function fond(Worksheet $ws, string $plage, string $rgb): void
    {
        $ws->getStyle($plage)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
    }

    private static function entetes(Worksheet $ws, int $ligne): void
    {
        foreach (ImportMatrice::COLONNES as $i => [$cle, $titre, $largeur, $obligatoire]) {
            $col = $i + 1;
            $ws->setCellValue([$col, $ligne], $titre.($obligatoire ? ' *' : ''));
            $ws->getColumnDimensionByColumn($col)->setWidth($largeur);
            $adresse = $ws->getCell([$col, $ligne])->getCoordinate();
            self::fond($ws, $adresse, $obligatoire ? self::BLEU_FONCE : self::BLEU_CLAIR);
            $ws->getStyle($adresse)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $ws->getStyle($adresse)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $ws->getRowDimension($ligne)->setRowHeight(44);
    }

    private static function modeEmploi(Worksheet $ws): void
    {
        $ws->setTitle("Mode d'emploi");
        $ws->setShowGridlines(false);
        $ws->getColumnDimension('A')->setWidth(4);
        $ws->getColumnDimension('B')->setWidth(36);
        $ws->getColumnDimension('C')->setWidth(100);

        $ws->setCellValue('B1', "Modèle d'import de la matrice — e-Tache");
        $ws->getStyle('B1')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::BLEU_FONCE);
        $ws->setCellValue('B2', "À remplir dans l'onglet « Matrice ». L'onglet « Exemple » montre le format attendu (il n'est pas importé).");
        $ws->getStyle('B2')->getFont()->setItalic(true);

        $r = 4;
        foreach (self::MODE_EMPLOI as [$titre, $texte]) {
            $ws->setCellValue("B{$r}", $titre);
            if ($texte === null) {
                $ws->getStyle("B{$r}:C{$r}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                self::fond($ws, "B{$r}:C{$r}", self::BLEU_FONCE);
            } else {
                $ws->setCellValue("C{$r}", $texte);
                $ws->getStyle("B{$r}")->getFont()->setBold(true);
                $ws->getStyle("B{$r}:C{$r}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $ws->getRowDimension($r)->setRowHeight(14 * max(1, (int) ceil(mb_strlen($texte) / 100)) + 2);
            }
            $r++;
        }

        $ws->setCellValue('B'.($r + 1), 'Légende des en-têtes');
        $ws->getStyle('B'.($r + 1))->getFont()->setBold(true);
        foreach ([['Colonne obligatoire *', self::BLEU_FONCE], ['Colonne facultative', self::BLEU_CLAIR]] as $k => [$texte, $couleur]) {
            $adresse = 'B'.($r + 2 + $k);
            $ws->setCellValue($adresse, $texte);
            self::fond($ws, $adresse, $couleur);
            $ws->getStyle($adresse)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
    }

    private static function matrice(Worksheet $ws, int $nbStatuts, int $nbPriorites): void
    {
        $ws->setTitle('Matrice');
        self::entetes($ws, 1);
        $ws->freezePane('A2');

        $fin = self::DERNIERE_LIGNE;
        $ws->getStyle("A2:T{$fin}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $ws->getStyle("N2:O{$fin}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $ws->getStyle("S2:S{$fin}")->getNumberFormat()->setFormatCode('#,##0');

        if ($nbPriorites > 0) {
            self::validation($ws, "P2:P{$fin}", DataValidation::TYPE_LIST, 'Valeur non autorisée', "Choisissez une priorité de l'onglet Listes (ou laissez vide).", 'Listes!$E$4:$E$'.(3 + $nbPriorites));
        }
        if ($nbStatuts > 0) {
            self::validation($ws, "Q2:Q{$fin}", DataValidation::TYPE_LIST, 'Valeur non autorisée', "Choisissez un statut de l'onglet Listes (ou laissez vide).", 'Listes!$D$4:$D$'.(3 + $nbStatuts));
        }
        self::validation($ws, "R2:R{$fin}", DataValidation::TYPE_WHOLE, 'Avancement', 'Entier de 0 à 100.', '0', '100');
        self::validation($ws, "N2:O{$fin}", DataValidation::TYPE_DATE, 'Date', 'Saisissez une date (JJ/MM/AAAA).', '36526', null, DataValidation::OPERATOR_GREATERTHAN);
    }

    private static function validation(Worksheet $ws, string $plage, string $type, string $titre, string $message, string $f1, ?string $f2 = null, ?string $operateur = null): void
    {
        $dv = new DataValidation;
        $dv->setType($type);
        $dv->setErrorStyle(DataValidation::STYLE_STOP);
        $dv->setAllowBlank(true);
        $dv->setShowDropDown(true); // PhpSpreadsheet inverse l'attribut OOXML : true = flèche de la liste visible
        $dv->setShowErrorMessage(true);
        $dv->setErrorTitle($titre);
        $dv->setError($message);
        $dv->setFormula1($f1);
        if ($f2 !== null) {
            $dv->setFormula2($f2);
        }
        if ($type === DataValidation::TYPE_WHOLE) {
            $dv->setOperator(DataValidation::OPERATOR_BETWEEN);
        } elseif ($operateur !== null) {
            $dv->setOperator($operateur);
        }
        $ws->setDataValidation($plage, $dv);
    }

    private static function exemple(Worksheet $ws, string $dir1, string $dir2): void
    {
        $ws->setTitle('Exemple');
        self::entetes($ws, 1);
        $ws->freezePane('A2');

        $ex = self::EXEMPLE;
        self::blocExemple($ws, 2, $ex, true, $dir1, $dir2);
        $ws->setCellValue('A6', "↑ Présentation COMPACTE : les valeurs ne sont écrites qu'à la première ligne (ou cellules fusionnées). Colonnes facultatives laissées vides sur certaines lignes.");
        $ws->setCellValue('A8', 'Même contenu en présentation COMPLÈTE : toutes les valeurs répétées sur chaque ligne ↓');
        self::entetes($ws, 9);
        self::blocExemple($ws, 10, $ex, false, $dir1, $dir2);
        $ws->setCellValue('A14', 'EXEMPLE : ne pas importer. Les matricules (MAT-001…) et emails sont fictifs : utilisez ceux de votre base.');
        foreach (['A6', 'A8', 'A14'] as $adresse) {
            $ws->getStyle($adresse)->getFont()->setBold(true)->getColor()->setRGB(self::ROUGE);
        }
    }

    private static function blocExemple(Worksheet $ws, int $ligne0, array $ex, bool $compact, string $dir1, string $dir2): void
    {
        $assoc = $dir2.($dir2 !== $dir1 ? '' : '');
        $precedente = null;
        foreach ($ex['taches'] as $k => $t) {
            $row = $ligne0 + $k;
            [$am, $tache, $indic, $livr, $resp, $pilote, $contrib, $debut, $fin, $prio, $statut, $avanc, $budget, $obs] = $t;

            $structure = $compact && $k > 0
                ? [null, null, null, null, null, null]
                : [$ex['axe'], $ex['resultat'], $ex['ac_code'], $ex['ac'], $dir1, $dir2 !== $dir1 ? $dir2 : null];
            $valeurs = array_merge($structure, [
                ($compact && $am === $precedente) ? null : $am,
                $tache, $indic, $livr, $resp, $pilote, $contrib, null, null, $prio, $statut, $avanc, $budget, $obs,
            ]);
            $precedente = $am;

            foreach ($valeurs as $i => $v) {
                if ($v !== null) {
                    $ws->setCellValue([$i + 1, $row], $v);
                }
            }
            foreach ([[14, $debut], [15, $fin]] as [$col, $date]) {
                if ($date) {
                    $ws->setCellValue([$col, $row], Date::PHPToExcel(new \DateTime($date)));
                }
            }
            $plage = "A{$row}:T{$row}";
            self::fond($ws, $plage, self::GRIS);
            $ws->getStyle($plage)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
            $ws->getStyle($plage)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('BFBFBF');
            $ws->getStyle("N{$row}:O{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $ws->getStyle("S{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getRowDimension($row)->setRowHeight(64);
        }
    }

    private static function listes(Worksheet $ws, array $directions, array $statuts, array $priorites): void
    {
        $ws->setTitle('Listes');
        $ws->setCellValue('A1', 'Valeurs autorisées dans votre application (codes des directions, statuts, priorités). Ces valeurs sont lues dans la base au moment du téléchargement.');
        $ws->getStyle('A1')->getFont()->setBold(true)->getColor()->setRGB(self::ROUGE);

        foreach ([['A', 'Code direction'], ['B', 'Direction'], ['D', 'Statuts'], ['E', 'Priorités']] as [$col, $titre]) {
            $ws->setCellValue("{$col}3", $titre);
            self::fond($ws, "{$col}3", self::BLEU_CLAIR);
            $ws->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }

        foreach (array_values($directions) as $k => $d) {
            $ws->setCellValue('A'.(4 + $k), $d['code']);
            $ws->setCellValue('B'.(4 + $k), $d['nom']);
        }
        foreach (array_values($statuts) as $k => $s) {
            $ws->setCellValue('D'.(4 + $k), $s);
        }
        foreach (array_values($priorites) as $k => $p) {
            $ws->setCellValue('E'.(4 + $k), $p);
        }

        if (! $directions) {
            $ws->setCellValue('A4', "Aucune direction n'a de code : renseignez-les dans la page Directions avant d'importer.");
            $ws->getStyle('A4')->getFont()->setBold(true)->getColor()->setRGB(self::ROUGE);
        }

        foreach (['A' => 18, 'B' => 66, 'C' => 4, 'D' => 18, 'E' => 16] as $col => $largeur) {
            $ws->getColumnDimension($col)->setWidth($largeur);
        }
    }
}
