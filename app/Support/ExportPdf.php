<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDompdf;

/**
 * Moteur PDF commun : tableau d'export (exports.tableau_pdf) et numérotation « Page x / y ».
 * Les couleurs, la police et le bandeau viennent de config/export_theme.php (via ExportTheme).
 */
class ExportPdf
{
    /**
     * PDF d'un export tabulaire.
     * $options : ['cartes' => [...], 'synthese' => [...]]  (mêmes structures que ExportExcel)
     */
    public static function tableau(string $fichier, string $titre, array $infos, array $entetes, array $lignes, array $options = [], string $orientation = 'landscape')
    {
        $entetes = array_values($entetes);
        $numeriques = [];
        $courtes = [];
        foreach ($entetes as $i => $e) {
            $numerique = $lignes !== [];
            foreach ($lignes as $l) {
                $v = array_values($l)[$i] ?? '';
                if ($v !== '' && $v !== null && ! is_int($v) && ! is_float($v)) {
                    $numerique = false;
                    break;
                }
            }
            $numeriques[$i] = $numerique;
            $max = 0;
            foreach ($lignes as $l) {
                $max = max($max, mb_strlen((string) (array_values($l)[$i] ?? '')));
            }
            $courtes[$i] = $max > 0 && $max <= 14; // codes, dates, statuts : jamais coupés sur 2 lignes
        }

        $pdf = Pdf::loadView('exports.tableau_pdf', [
            'titre' => $titre,
            'infos' => array_values(array_filter($infos, fn ($i) => filled($i))),
            'entetes' => $entetes,
            'lignes' => $lignes,
            'numeriques' => $numeriques,
            'courtes' => $courtes,
            'cartes' => $options['cartes'] ?? [],
            'synthese' => $options['synthese'] ?? [],
            'mention' => Reglages::get('exports.mention_pdf'),
        ])->setPaper('a4', $orientation);

        return self::paginer($pdf)->download($fichier);
    }

    /**
     * Ajoute « Page x / y » en bas à droite de chaque page.
     * À appeler sur n'importe quel PDF : ExportPdf::paginer(Pdf::loadView(...)->setPaper(...))->stream('x.pdf')
     */
    public static function paginer(PdfDompdf $pdf, int $margeDroite = 30, int $margeBas = 26): PdfDompdf
    {
        $pdf->output(); // force le rendu pour connaître le nombre de pages
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $metrics = $dompdf->getFontMetrics();
        $police = $metrics->getFont(ExportTheme::policePdf(), 'bold') ?: $metrics->getFont('Helvetica', 'bold');
        $taille = 9;
        $largeur = $metrics->getTextWidth('Page 99 / 99', $police, $taille);
        $couleur = self::rgb01(ExportTheme::rgb('primaire'));

        $canvas->page_text($canvas->get_width() - $margeDroite - $largeur, $canvas->get_height() - $margeBas, 'Page {PAGE_NUM} / {PAGE_COUNT}', $police, $taille, $couleur);

        return $pdf;
    }

    private static function rgb01(string $rgb): array
    {
        return [hexdec(substr($rgb, 0, 2)) / 255, hexdec(substr($rgb, 2, 2)) / 255, hexdec(substr($rgb, 4, 2)) / 255];
    }
}
