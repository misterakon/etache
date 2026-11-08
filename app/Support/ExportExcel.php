<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Moteur Excel commun : bandeau, titre, informations, cartes de chiffres clés (option),
 * tableau avec en-tête figé, lignes alternées et filtres automatiques.
 * Toutes les couleurs viennent de config/export_theme.php (via ExportTheme).
 *
 * Utilisation (inchangée) : ExportExcel::telecharger($fichier, $titre, $infos, $entetes, $lignes)
 * Option : un 6e argument ['cartes' => [...], 'synthese' => [...]] ajoute une feuille « Synthèse ».
 *   cartes   : [['libelle' => 'Paiements', 'valeur' => 359, 'unite' => '%' (facultatif)], ...]   (4 au plus)
 *   synthese : [['titre' => 'Par statut', 'entetes' => ['Statut','Tâches','Part'], 'lignes' => [[...]]], ...]
 */
class ExportExcel
{
    public static function telecharger(string $fichier, string $titre, array $infos, array $entetes, array $lignes, array $options = []): StreamedResponse
    {
        $classeur = new Spreadsheet;
        $classeur->getDefaultStyle()->getFont()->setName(ExportTheme::policeExcel())->setSize(10);
        $classeur->getProperties()->setCreator(ExportTheme::application())->setTitle($titre)->setCompany(ExportTheme::application());

        $cartes = $options['cartes'] ?? [];
        $synthese = $options['synthese'] ?? [];
        $infos = array_values(array_filter($infos, fn ($i) => filled($i)));

        if ($cartes || $synthese) {
            self::feuilleSynthese($classeur->getActiveSheet(), $titre, $infos, $cartes, $synthese);
            self::feuilleDonnees($classeur->createSheet(), 'Données', $titre, $infos, $entetes, $lignes);
        } else {
            self::feuilleDonnees($classeur->getActiveSheet(), $titre, $titre, $infos, $entetes, $lignes);
        }
        $classeur->setActiveSheetIndex(0);

        return self::reponse($classeur, $fichier);
    }

    public static function reponse(Spreadsheet $classeur, string $fichier): StreamedResponse
    {
        return response()->streamDownload(
            fn () => (new Xlsx($classeur))->save('php://output'),
            $fichier,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    // ------------------------------------------------------------------ briques réutilisables

    /** Bandeau orange + vert en haut de la feuille (ligne donnée, sur $nbCols colonnes). */
    public static function bandeau(Worksheet $f, int $ligne, int $nbCols): void
    {
        $nbAccent = max(1, (int) ceil($nbCols * ExportTheme::partAccent() / 100));
        for ($c = 1; $c <= $nbCols; $c++) {
            $f->getStyle(Coordinate::stringFromColumnIndex($c).$ligne)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ExportTheme::rgb($c <= $nbAccent ? 'accent' : 'primaire'));
        }
        $f->getRowDimension($ligne)->setRowHeight(9);
    }

    public static function styleTitre(Worksheet $f, string $cellule, int $taille = 20): void
    {
        $f->getStyle($cellule)->getFont()->setBold(true)->setSize($taille)->getColor()->setRGB(ExportTheme::rgb('primaire'));
    }

    /** Lignes d'information : « Édité le… » en gris, les autres (filtres) en orange gras. Retourne la ligne suivante. */
    public static function infos(Worksheet $f, int $ligne, array $infos): int
    {
        foreach ($infos as $info) {
            $f->setCellValue('A'.$ligne, $info);
            $edition = str_starts_with((string) $info, 'Édité') || str_starts_with((string) $info, 'Exporté');
            $f->getStyle('A'.$ligne)->getFont()->setSize(9)->setBold(! $edition)
                ->getColor()->setRGB(ExportTheme::rgb($edition ? 'texte_doux' : 'accent_texte'));
            $ligne++;
        }

        return $ligne;
    }

    /** En-tête vert, texte blanc gras. */
    public static function styleEntete(Worksheet $f, int $ligne, int $nbCols): void
    {
        $derniere = Coordinate::stringFromColumnIndex($nbCols);
        $f->getStyle("A{$ligne}:{$derniere}{$ligne}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => ExportTheme::rgb('entete_texte')]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ExportTheme::rgb('primaire')]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $f->getRowDimension($ligne)->setRowHeight(26);
    }

    /** Filets et lignes alternées (règle conditionnelle : reste juste après un tri ou un filtre). */
    public static function corps(Worksheet $f, int $debut, int $fin, int $nbCols): void
    {
        $plage = 'A'.$debut.':'.Coordinate::stringFromColumnIndex($nbCols).$fin;
        $f->getStyle($plage)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => ExportTheme::rgb('ligne_bordure')]]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
            'font' => ['color' => ['rgb' => ExportTheme::rgb('texte')]],
        ]);
        $zebre = new Conditional;
        $zebre->setConditionType(Conditional::CONDITION_EXPRESSION)->addCondition('MOD(ROW(),2)=0');
        $remplissage = $zebre->getStyle()->getFill();
        $remplissage->setFillType(Fill::FILL_SOLID);
        $remplissage->getStartColor()->setRGB(ExportTheme::rgb('ligne_alternee'));
        $remplissage->getEndColor()->setRGB(ExportTheme::rgb('ligne_alternee'));
        $f->getStyle($plage)->setConditionalStyles([$zebre]);
    }

    /** Impression : A4 paysage, une page de large, en-tête répété, pied « e-Tâche · titre · Page x / y ». */
    public static function miseEnPage(Worksheet $f, string $titre, ?int $ligneEntete = null): void
    {
        $f->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
        $f->getPageMargins()->setTop(0.5)->setBottom(0.6)->setLeft(0.4)->setRight(0.4);
        if ($ligneEntete) {
            $f->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($ligneEntete, $ligneEntete);
        }
        $f->getHeaderFooter()->setOddFooter('&L&8'.str_replace('&', '&&', ExportTheme::application().' · '.$titre.' · '.now()->format('d/m/Y')).'&R&8&"-,Bold"Page &P / &N');
    }

    // ------------------------------------------------------------------ feuille de données

    private static function feuilleDonnees(Worksheet $f, string $nomFeuille, string $titre, array $infos, array $entetes, array $lignes): void
    {
        $f->setTitle(mb_substr(preg_replace('/[\\\\\/?*:\[\]]/', ' ', $nomFeuille), 0, 31));
        $f->setShowGridlines(false);
        $f->getTabColor()->setRGB(ExportTheme::rgb('primaire'));

        $entetes = array_values($entetes);
        $nbCols = max(count($entetes), 1);
        $derniere = Coordinate::stringFromColumnIndex($nbCols);

        self::bandeau($f, 1, $nbCols);
        $f->getRowDimension(2)->setRowHeight(8);
        $f->setCellValue('A3', $titre);
        self::styleTitre($f, 'A3');
        $f->getRowDimension(3)->setRowHeight(30);
        $suivante = self::infos($f, 4, $infos);

        $ligneEntete = $suivante + 1;
        $premiere = $ligneEntete + 1;
        $derniereLigne = $ligneEntete + max(count($lignes), 1);

        $f->fromArray($entetes, null, 'A'.$ligneEntete);
        if ($lignes) {
            $f->fromArray($lignes, null, 'A'.$premiere);
            self::neutraliserFormules($f, $lignes, $premiere);
        } else {
            $f->setCellValue('A'.$premiere, 'Aucune donnée pour ces critères.');
            $f->mergeCells("A{$premiere}:{$derniere}{$premiere}");
            $f->getStyle('A'.$premiere)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $f->getStyle('A'.$premiere)->getFont()->setItalic(true)->getColor()->setRGB(ExportTheme::rgb('texte_doux'));
        }

        self::styleEntete($f, $ligneEntete, $nbCols);
        self::corps($f, $premiere, $derniereLigne, $nbCols);
        $f->freezePane('A'.$premiere);
        $f->setAutoFilter("A{$ligneEntete}:{$derniere}{$derniereLigne}");

        // Largeur selon le contenu (bornée), retour à la ligne, alignement et format des nombres
        foreach ($entetes as $i => $enteteCol) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $max = mb_strlen((string) $enteteCol);
            $numerique = $lignes !== [];
            foreach ($lignes as $l) {
                $v = $l[$i] ?? '';
                $max = max($max, mb_strlen((string) $v));
                if ($v !== '' && $v !== null && ! is_int($v) && ! is_float($v)) {
                    $numerique = false;
                }
            }
            $f->getColumnDimension($col)->setWidth(min(max($max + 2, 10), 50));
            // NB : getStyle() partage un objet entre appels : toujours l'appeler juste avant de l'utiliser.
            $horizontal = $numerique ? Alignment::HORIZONTAL_RIGHT : Alignment::HORIZONTAL_LEFT;
            $plageCorps = $col.$premiere.':'.$col.$derniereLigne;
            $f->getStyle($plageCorps)->applyFromArray(['alignment' => ['horizontal' => $horizontal, 'indent' => 1, 'wrapText' => true]]);
            $f->getStyle($col.$ligneEntete)->applyFromArray(['alignment' => ['horizontal' => $horizontal, 'indent' => 1]]);
            if ($numerique && preg_match('/FCFA|montant|budget|brut|net|frais|total/i', (string) $enteteCol)) {
                $f->getStyle($plageCorps)->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        self::miseEnPage($f, $titre, $ligneEntete);
    }

    /** Un texte qui commence par « = » ne doit jamais devenir une formule. */
    private static function neutraliserFormules(Worksheet $f, array $lignes, int $premiere): void
    {
        foreach ($lignes as $r => $ligne) {
            foreach (array_values($ligne) as $c => $v) {
                if (is_string($v) && str_starts_with($v, '=')) {
                    $f->setCellValueExplicit(Coordinate::stringFromColumnIndex($c + 1).($premiere + $r), $v, DataType::TYPE_STRING);
                }
            }
        }
    }

    // ------------------------------------------------------------------ feuille de synthèse (cartes + tableaux)

    /** Colonnes : A-B carte 1, C marge, D-E carte 2, F marge, G-H carte 3, I marge, J-K carte 4. */
    private static function feuilleSynthese(Worksheet $f, string $titre, array $infos, array $cartes, array $synthese): void
    {
        $f->setTitle('Synthèse');
        $f->setShowGridlines(false);
        $f->getTabColor()->setRGB(ExportTheme::rgb('accent'));
        foreach (['A' => 13, 'B' => 13, 'C' => 2, 'D' => 13, 'E' => 13, 'F' => 2, 'G' => 13, 'H' => 13, 'I' => 2, 'J' => 13, 'K' => 13] as $c => $l) {
            $f->getColumnDimension($c)->setWidth($l);
        }

        self::bandeau($f, 1, 11);
        $f->getRowDimension(2)->setRowHeight(8);
        $f->setCellValue('A3', $titre);
        self::styleTitre($f, 'A3', 24);
        $f->getRowDimension(3)->setRowHeight(34);
        $ligne = self::infos($f, 4, $infos) + 1;

        // Cartes
        $colonnes = [['A', 'B'], ['D', 'E'], ['G', 'H'], ['J', 'K']];
        foreach (array_slice(array_values($cartes), 0, 4) as $i => $carte) {
            [$c1, $c2] = $colonnes[$i];
            $couleur = ExportTheme::carte($i);
            $f->mergeCells("{$c1}{$ligne}:{$c2}{$ligne}");
            $f->mergeCells("{$c1}".($ligne + 1).":{$c2}".($ligne + 1));
            $f->setCellValue("{$c1}{$ligne}", $carte['libelle'] ?? '');
            $f->setCellValue("{$c1}".($ligne + 1), $carte['valeur'] ?? '');
            $f->getStyle("{$c1}{$ligne}:{$c2}".($ligne + 1))->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ExportTheme::rgb('fond_carte')]],
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => ExportTheme::rgb('bordure_carte')]],
                    'left' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => $couleur]],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            ]);
            $f->getStyle("{$c1}{$ligne}")->getFont()->setSize(9)->getColor()->setRGB(ExportTheme::rgb('texte_doux'));
            $f->getStyle("{$c1}".($ligne + 1))->getFont()->setSize(20)->setBold(true)->getColor()->setRGB(ExportTheme::rgb('primaire'));
            if (is_numeric($carte['valeur'] ?? null)) {
                $format = is_int($carte['valeur']) ? '#,##0' : '#,##0.0';
                if (! empty($carte['unite'])) {
                    $format .= ' "'.str_replace('"', '', $carte['unite']).'"';
                }
                $f->getStyle("{$c1}".($ligne + 1))->getNumberFormat()->setFormatCode($format);
            }
        }
        if ($cartes) {
            $f->getRowDimension($ligne)->setRowHeight(20);
            $f->getRowDimension($ligne + 1)->setRowHeight(34);
            $ligne += 3;
        }

        // Tableaux de synthèse, deux par ligne : gauche A-E (étiquette A:C, valeurs D, E), droite G-K (étiquette G:I, valeurs J, K)
        $gabarits = [['A', 'C', 'D', 'E'], ['G', 'I', 'J', 'K']];
        foreach (array_chunk(array_values($synthese), 2) as $paire) {
            $haut = $ligne;
            $fin = $haut;
            foreach ($paire as $k => $tab) {
                [$debut, $finLabel, $v1, $v2] = $gabarits[$k];
                $y = $haut;
                $f->setCellValue("{$debut}{$y}", $tab['titre'] ?? '');
                $f->getStyle("{$debut}{$y}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(ExportTheme::rgb('primaire'));
                $f->getStyle("{$debut}{$y}:{$finLabel}{$y}")->applyFromArray(['borders' => ['bottom' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => ExportTheme::rgb('accent')]]]]);
                $y += 2;

                $entetesT = array_values($tab['entetes'] ?? []);
                $f->mergeCells("{$debut}{$y}:{$finLabel}{$y}");
                $f->setCellValue("{$debut}{$y}", $entetesT[0] ?? '');
                $f->setCellValue("{$v1}{$y}", $entetesT[1] ?? '');
                $f->setCellValue("{$v2}{$y}", $entetesT[2] ?? '');
                self::styleEnteteBloc($f, "{$debut}{$y}:{$v2}{$y}");
                $f->getStyle("{$v1}{$y}:{$v2}{$y}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $y++;

                foreach ($tab['lignes'] ?? [] as $n => $l) {
                    $l = array_values($l);
                    $f->mergeCells("{$debut}{$y}:{$finLabel}{$y}");
                    $f->setCellValue("{$debut}{$y}", $l[0] ?? '');
                    $f->setCellValue("{$v1}{$y}", $l[1] ?? '');
                    $f->setCellValue("{$v2}{$y}", $l[2] ?? '');
                    $f->getStyle("{$debut}{$y}")->getFont()->setBold(true);
                    $f->getStyle("{$v1}{$y}:{$v2}{$y}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    if (is_numeric($l[1] ?? null)) {
                        $f->getStyle("{$v1}{$y}")->getNumberFormat()->setFormatCode('#,##0');
                    }
                    $f->getStyle("{$debut}{$y}:{$v2}{$y}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $n % 2 === 0 ? 'FFFFFF' : ExportTheme::rgb('ligne_alternee')]],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => ExportTheme::rgb('ligne_bordure')]]],
                    ]);
                    $y++;
                }
                $fin = max($fin, $y);
            }
            $ligne = $fin + 2;
        }

        self::miseEnPage($f, $titre);
    }

    private static function styleEnteteBloc(Worksheet $f, string $plage): void
    {
        $f->getStyle($plage)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => ExportTheme::rgb('entete_texte')]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ExportTheme::rgb('primaire')]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }
}
