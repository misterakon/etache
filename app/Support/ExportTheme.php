<?php

namespace App\Support;

/**
 * Lecture du thème des exports (config/export_theme.php).
 * Toutes les couleurs des PDF et Excel passent par ici : aucune couleur en dur ailleurs.
 */
class ExportTheme
{
    /** Valeurs de secours si la config n'est pas publiée ou si un code est invalide. */
    private const DEFAUT = [
        'primaire' => '0B6B45', 'accent' => 'F7941D', 'accent_texte' => 'C2570A',
        'texte' => '1F2933', 'texte_doux' => '6B7280', 'entete_texte' => 'FFFFFF',
        'fond_carte' => 'F1F7F3', 'bordure_carte' => 'DCE9E1', 'ligne_alternee' => 'EEF6F1', 'ligne_bordure' => 'E3E8E5',
        'danger' => 'C0392B', 'fiche_bandeau' => '6B6B6B',
    ];

    /** Code couleur sans « # », en majuscules (format attendu par Excel). */
    public static function rgb(string $cle): string
    {
        $v = $cle;
        if (! preg_match('/^#?[0-9a-fA-F]{6}$/', $cle)) {
            $v = config('export_theme.couleurs.'.$cle, self::DEFAUT[$cle] ?? '000000');
        }
        $v = strtoupper(ltrim((string) $v, '#'));

        return preg_match('/^[0-9A-F]{6}$/', $v) ? $v : (self::DEFAUT[$cle] ?? '000000');
    }

    /** Code couleur avec « # » (format CSS pour les PDF). */
    public static function hex(string $cle): string
    {
        return '#'.self::rgb($cle);
    }

    /** Couleur de la barre de la n-ième carte de chiffres clés (cycle). */
    public static function carte(int $i): string
    {
        $liste = array_values(config('export_theme.cartes', ['primaire', 'accent']));

        return self::rgb($liste[$i % max(count($liste), 1)] ?? 'primaire');
    }

    private const TAILLES = [
        'texte' => 10, 'tableau' => 11, 'entete' => 11, 'infos' => 11, 'titre' => 26,
        'section' => 14, 'carte_libelle' => 10, 'carte_valeur' => 19, 'pied' => 9,
    ];

    /** Taille de texte des PDF, au format CSS (ex. « 11px »). */
    public static function taille(string $cle): string
    {
        $v = (float) config('export_theme.tailles.'.$cle, self::TAILLES[$cle] ?? 10);

        return rtrim(rtrim(number_format(max(6, min(48, $v)), 1, '.', ''), '0'), '.').'px';
    }

    public static function application(): string
    {
        return (string) config('export_theme.application', 'e-Tâche');
    }

    public static function policePdf(): string
    {
        return (string) config('export_theme.police_pdf', 'Helvetica');
    }

    public static function policeExcel(): string
    {
        return (string) config('export_theme.police_excel', 'Calibri');
    }

    public static function partAccent(): int
    {
        return max(5, min(95, (int) config('export_theme.bandeau.part_accent', 25)));
    }

    public static function hauteurBandeau(): int
    {
        return max(4, (int) config('export_theme.bandeau.hauteur_pdf', 9));
    }
}
