<?php

namespace App\Support;

/**
 * Graphiques en SVG calculés côté serveur : aucune bibliothèque JavaScript, rendu identique à l'écran, à l'impression et dans les PDF,
 * infobulles natives (<title>). Couleurs en hexadécimal simple pour rester lisibles par le générateur de PDF.
 */
class GraphiquesSvg
{
    private const POLICE = 'Arial, Helvetica, sans-serif';

    public const COULEURS = ['#0F8B8D', '#2B6CB0', '#7B6BE0', '#C77D0A', '#2E7D5B', '#D9577A', '#8A94A6'];

    private static function e($s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function court(string $s, int $n): string
    {
        return mb_strlen($s) > $n ? mb_substr($s, 0, $n - 1).'…' : $s;
    }

    private static function f(float $v): string
    {
        return sprintf('%F', round($v, 2));
    }

    private static function ouvrir(int $l, int $h, string $titre): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$l.' '.$h.'" width="'.$l.'" height="'.$h.'" style="width:100%;height:auto;max-width:100%;font-family:'.self::POLICE.'" role="img" aria-label="'.self::e($titre).'"><title>'.self::e($titre).'</title>';
    }

    private static function vide(string $titre, string $message = 'Aucune donnée à afficher'): string
    {
        return self::ouvrir(420, 90, $titre).'<text x="210" y="50" text-anchor="middle" font-size="13" fill="#8A94A6">'.self::e($message).'</text></svg>';
    }

    /** @param list<array{libelle: string, valeur: float|int, couleur?: string}> $parts */
    public static function donut(array $parts, string $titre, string $centre = '', string $sousCentre = '', int $taille = 210): string
    {
        $total = array_sum(array_column($parts, 'valeur'));
        if ($total <= 0) {
            return self::vide($titre);
        }
        $largeur = $taille + 230;
        $hauteur = max($taille, 30 + count($parts) * 26);
        $cx = $taille / 2;
        $cy = $hauteur / 2;
        $R = $taille / 2 - 6;
        $r = $R * 0.62;
        $pt = fn (float $rayon, float $angle) => [$cx + $rayon * cos(deg2rad($angle)), $cy + $rayon * sin(deg2rad($angle))];

        $svg = self::ouvrir($largeur, $hauteur, $titre);
        $a1 = -90.0;
        foreach (array_values($parts) as $i => $p) {
            if ($p['valeur'] <= 0) {
                continue;
            }
            $couleur = $p['couleur'] ?? self::COULEURS[$i % count(self::COULEURS)];
            $a2 = min($a1 + 360 * $p['valeur'] / $total, $a1 + 359.99);
            [$x1, $y1] = $pt($R, $a1);
            [$x2, $y2] = $pt($R, $a2);
            [$x3, $y3] = $pt($r, $a2);
            [$x4, $y4] = $pt($r, $a1);
            $grand = ($a2 - $a1) > 180 ? 1 : 0;
            $svg .= '<path d="M'.self::f($x1).' '.self::f($y1).' A'.self::f($R).' '.self::f($R).' 0 '.$grand.' 1 '.self::f($x2).' '.self::f($y2).' L'.self::f($x3).' '.self::f($y3).' A'.self::f($r).' '.self::f($r).' 0 '.$grand.' 0 '.self::f($x4).' '.self::f($y4).' Z" fill="'.$couleur.'" stroke="#FFFFFF" stroke-width="2"><title>'.self::e($p['libelle']).' : '.$p['valeur'].' ('.round($p['valeur'] / $total * 100).' %)</title></path>';
            $a1 = $a2;
        }
        $svg .= '<text x="'.self::f($cx).'" y="'.self::f($cy + 2).'" text-anchor="middle" font-size="28" font-weight="bold" fill="#14304F">'.self::e($centre !== '' ? $centre : (string) $total).'</text>';
        if ($sousCentre !== '') {
            $svg .= '<text x="'.self::f($cx).'" y="'.self::f($cy + 22).'" text-anchor="middle" font-size="12" fill="#52637C">'.self::e($sousCentre).'</text>';
        }
        $y = ($hauteur - count($parts) * 26) / 2 + 14;
        foreach (array_values($parts) as $i => $p) {
            $couleur = $p['couleur'] ?? self::COULEURS[$i % count(self::COULEURS)];
            $svg .= '<rect x="'.($taille + 18).'" y="'.self::f($y - 11).'" width="13" height="13" rx="3" fill="'.$couleur.'"/>'
                .'<text x="'.($taille + 38).'" y="'.self::f($y).'" font-size="13" fill="#14304F">'.self::e(self::court($p['libelle'], 26)).' : <tspan font-weight="bold">'.$p['valeur'].'</tspan></text>';
            $y += 26;
        }

        return $svg.'</svg>';
    }

    /** Barres horizontales. @param list<array{libelle: string, valeur: float|int, couleur?: string, texte?: string}> $lignes */
    public static function barresH(array $lignes, string $titre, ?float $max = null, string $suffixe = '', int $largeur = 560): string
    {
        if ($lignes === []) {
            return self::vide($titre);
        }
        $max = $max ?? max(1, max(array_column($lignes, 'valeur')));
        $colLibelle = 190;
        $zone = $largeur - $colLibelle - 70;
        $hauteur = count($lignes) * 30 + 12;
        $svg = self::ouvrir($largeur, $hauteur, $titre);
        foreach (array_values($lignes) as $i => $l) {
            $y = 8 + $i * 30;
            $w = max($l['valeur'] > 0 ? 3 : 0, $zone * min(1, $l['valeur'] / max($max, 0.0001)));
            $couleur = $l['couleur'] ?? '#0F8B8D';
            $svg .= '<text x="'.($colLibelle - 10).'" y="'.($y + 15).'" text-anchor="end" font-size="12.5" fill="#14304F">'.self::e(self::court($l['libelle'], 30)).'</text>'
                .'<rect x="'.$colLibelle.'" y="'.$y.'" width="'.$zone.'" height="20" rx="5" fill="#EEF1F5"/>'
                .'<rect x="'.$colLibelle.'" y="'.$y.'" width="'.self::f($w).'" height="20" rx="5" fill="'.$couleur.'"><title>'.self::e($l['libelle']).' : '.self::e($l['texte'] ?? ($l['valeur'].$suffixe)).'</title></rect>'
                .'<text x="'.self::f($colLibelle + $w + 8).'" y="'.($y + 15).'" font-size="12.5" font-weight="bold" fill="#14304F">'.self::e($l['texte'] ?? ($l['valeur'].$suffixe)).'</text>';
        }

        return $svg.'</svg>';
    }

    /**
     * Barres horizontales en tableau HTML (pour le PDF : le générateur de PDF place mal les textes des SVG larges).
     *
     * @param  list<array{libelle: string, valeur: float|int, couleur?: string, texte?: string}>  $lignes
     */
    public static function barresHtml(array $lignes, ?float $max = null, string $suffixe = ''): string
    {
        if ($lignes === []) {
            return '<p style="font-size:9px;color:#8A94A6;">Aucune donnée à afficher.</p>';
        }
        $max = $max ?? max(1, max(array_column($lignes, 'valeur')));
        $h = '<table style="width:100%;border-collapse:collapse;">';
        foreach ($lignes as $l) {
            $pct = $l['valeur'] > 0 ? max(2, min(100, $l['valeur'] / max($max, 0.0001) * 100)) : 0;
            $h .= '<tr><td style="width:36%;padding:3px 8px 3px 0;font-size:8.5px;color:#14304F;text-align:right;">'.htmlspecialchars($l['libelle'], ENT_QUOTES, 'UTF-8').'</td>'
                .'<td style="padding:3px 0;"><div style="background:#EEF1F5;height:11px;"><div style="background:'.($l['couleur'] ?? '#0F8B8D').';height:11px;width:'.round($pct, 1).'%;"></div></div></td>'
                .'<td style="width:24%;padding:3px 0 3px 8px;font-size:8.5px;font-weight:bold;color:#14304F;">'.htmlspecialchars($l['texte'] ?? ($l['valeur'].$suffixe), ENT_QUOTES, 'UTF-8').'</td></tr>';
        }

        return $h.'</table>';
    }

    /**
     * Barres horizontales empilées (ex. statuts par direction).
     *
     * @param  list<array{libelle: string, segments: list<array{valeur: int, couleur: string, libelle: string}>}>  $lignes
     * @param  list<array{libelle: string, couleur: string}>  $legende
     */
    public static function empilees(array $lignes, array $legende, string $titre, int $largeur = 600): string
    {
        $lignes = array_values(array_filter($lignes, fn ($l) => array_sum(array_column($l['segments'], 'valeur')) > 0));
        if ($lignes === []) {
            return self::vide($titre);
        }
        $colLibelle = 190;
        $zone = $largeur - $colLibelle - 50;
        $max = max(array_map(fn ($l) => array_sum(array_column($l['segments'], 'valeur')), $lignes));
        $hauteur = 38 + count($lignes) * 30 + 8;
        $svg = self::ouvrir($largeur, $hauteur, $titre);
        $x = $colLibelle;
        foreach ($legende as $item) {
            $svg .= '<rect x="'.$x.'" y="8" width="11" height="11" rx="3" fill="'.$item['couleur'].'"/><text x="'.($x + 16).'" y="18" font-size="11.5" fill="#52637C">'.self::e($item['libelle']).'</text>';
            $x += 22 + mb_strlen($item['libelle']) * 6.6;
        }
        foreach ($lignes as $i => $l) {
            $y = 34 + $i * 30;
            $total = array_sum(array_column($l['segments'], 'valeur'));
            $svg .= '<text x="'.($colLibelle - 10).'" y="'.($y + 15).'" text-anchor="end" font-size="12.5" fill="#14304F">'.self::e(self::court($l['libelle'], 30)).'</text>';
            $px = (float) $colLibelle;
            foreach ($l['segments'] as $s) {
                if ($s['valeur'] <= 0) {
                    continue;
                }
                $w = $zone * $s['valeur'] / $max;
                $svg .= '<rect x="'.self::f($px).'" y="'.$y.'" width="'.self::f($w).'" height="20" fill="'.$s['couleur'].'"><title>'.self::e($l['libelle']).' — '.self::e($s['libelle']).' : '.$s['valeur'].'</title></rect>';
                $px += $w;
            }
            $svg .= '<text x="'.self::f($px + 8).'" y="'.($y + 15).'" font-size="12.5" font-weight="bold" fill="#14304F">'.$total.'</text>';
        }

        return $svg.'</svg>';
    }

    /**
     * Courbes (ex. attendu / réalisé). Les valeurs null interrompent la courbe.
     *
     * @param  list<string>  $labels
     * @param  list<array{nom: string, valeurs: list<float|int|null>, couleur: string, pointille?: bool}>  $series
     */
    public static function courbes(array $labels, array $series, string $titre, float $ymax = 100, string $suffixe = ' %', int $largeur = 640, int $hauteur = 270): string
    {
        $n = count($labels);
        if ($n < 2 || $series === []) {
            return self::vide($titre, 'Pas assez de points pour tracer une courbe');
        }
        $g = 46;
        $d = 18;
        $h = 18;
        $b = 62;
        $zoneL = $largeur - $g - $d;
        $zoneH = $hauteur - $h - $b;
        $x = fn (int $i) => $g + $zoneL * $i / ($n - 1);
        $y = fn (float $v) => $h + $zoneH * (1 - min($v, $ymax) / $ymax);
        $svg = self::ouvrir($largeur, $hauteur, $titre);
        for ($k = 0; $k <= 5; $k++) {
            $v = $ymax * $k / 5;
            $svg .= '<line x1="'.$g.'" y1="'.self::f($y($v)).'" x2="'.($largeur - $d).'" y2="'.self::f($y($v)).'" stroke="#E3E8EF" stroke-width="1"/>'
                .'<text x="'.($g - 8).'" y="'.self::f($y($v) + 4).'" text-anchor="end" font-size="11" fill="#6B7A90">'.round($v).'</text>';
        }
        $pas = max(1, (int) ceil($n / 12));
        foreach ($labels as $i => $lib) {
            if ($i % $pas === 0 || $i === $n - 1) {
                $svg .= '<text x="'.self::f($x($i)).'" y="'.($h + $zoneH + 18).'" text-anchor="middle" font-size="11" fill="#6B7A90">'.self::e($lib).'</text>';
            }
        }
        foreach ($series as $s) {
            $segment = [];
            $traits = [];
            foreach ($s['valeurs'] as $i => $v) {
                if ($v === null) {
                    if ($segment) {
                        $traits[] = $segment;
                        $segment = [];
                    }

                    continue;
                }
                $segment[] = self::f($x($i)).','.self::f($y((float) $v));
            }
            if ($segment) {
                $traits[] = $segment;
            }
            foreach ($traits as $t) {
                $svg .= '<polyline points="'.implode(' ', $t).'" fill="none" stroke="'.$s['couleur'].'" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"'.(! empty($s['pointille']) ? ' stroke-dasharray="7 6"' : '').'/>';
            }
            foreach ($s['valeurs'] as $i => $v) {
                if ($v !== null) {
                    $svg .= '<circle cx="'.self::f($x($i)).'" cy="'.self::f($y((float) $v)).'" r="4" fill="#FFFFFF" stroke="'.$s['couleur'].'" stroke-width="2.5"><title>'.self::e($s['nom']).' — '.self::e($labels[$i]).' : '.$v.$suffixe.'</title></circle>';
                }
            }
        }
        $lx = $g;
        foreach ($series as $s) {
            $svg .= '<line x1="'.$lx.'" y1="'.($hauteur - 22).'" x2="'.($lx + 24).'" y2="'.($hauteur - 22).'" stroke="'.$s['couleur'].'" stroke-width="3"'.(! empty($s['pointille']) ? ' stroke-dasharray="6 4"' : '').'/>'
                .'<text x="'.($lx + 30).'" y="'.($hauteur - 18).'" font-size="12" fill="#14304F">'.self::e($s['nom']).'</text>';
            $lx += 52 + mb_strlen($s['nom']) * 6.8;
        }

        return $svg.'</svg>';
    }

    /**
     * Colonnes groupées (ex. courrier reçu / expédié par mois).
     *
     * @param  list<string>  $labels
     * @param  list<array{nom: string, valeurs: list<int>, couleur: string}>  $series
     */
    public static function colonnes(array $labels, array $series, string $titre, int $largeur = 640, int $hauteur = 250): string
    {
        $max = max(1, ...array_map(fn ($s) => max($s['valeurs'] ?: [0]), $series));
        if (array_sum(array_map(fn ($s) => array_sum($s['valeurs']), $series)) === 0) {
            return self::vide($titre);
        }
        $pasMax = max(1, (int) ceil($max / 5));
        $max = $pasMax * 5;
        $g = 40;
        $d = 12;
        $h = 14;
        $b = 62;
        $zoneL = $largeur - $g - $d;
        $zoneH = $hauteur - $h - $b;
        $n = count($labels);
        $groupe = $zoneL / $n;
        $col = min(22, ($groupe - 8) / max(1, count($series)));
        $svg = self::ouvrir($largeur, $hauteur, $titre);
        for ($k = 0; $k <= 5; $k++) {
            $v = $pasMax * $k;
            $yy = $h + $zoneH * (1 - $v / $max);
            $svg .= '<line x1="'.$g.'" y1="'.self::f($yy).'" x2="'.($largeur - $d).'" y2="'.self::f($yy).'" stroke="#E3E8EF"/><text x="'.($g - 8).'" y="'.self::f($yy + 4).'" text-anchor="end" font-size="11" fill="#6B7A90">'.$v.'</text>';
        }
        foreach ($labels as $i => $lib) {
            $x0 = $g + $groupe * $i + ($groupe - $col * count($series)) / 2;
            foreach ($series as $j => $s) {
                $v = $s['valeurs'][$i] ?? 0;
                $hh = $zoneH * $v / $max;
                $svg .= '<rect x="'.self::f($x0 + $col * $j).'" y="'.self::f($h + $zoneH - $hh).'" width="'.self::f($col - 2).'" height="'.self::f($hh).'" rx="3" fill="'.$s['couleur'].'"><title>'.self::e($s['nom']).' — '.self::e($lib).' : '.$v.'</title></rect>';
            }
            $svg .= '<text x="'.self::f($g + $groupe * $i + $groupe / 2).'" y="'.($h + $zoneH + 17).'" text-anchor="middle" font-size="11" fill="#6B7A90">'.self::e($lib).'</text>';
        }
        $lx = $g;
        foreach ($series as $s) {
            $svg .= '<rect x="'.$lx.'" y="'.($hauteur - 28).'" width="12" height="12" rx="3" fill="'.$s['couleur'].'"/><text x="'.($lx + 18).'" y="'.($hauteur - 18).'" font-size="12" fill="#14304F">'.self::e($s['nom']).'</text>';
            $lx += 44 + mb_strlen($s['nom']) * 6.8;
        }

        return $svg.'</svg>';
    }
}
