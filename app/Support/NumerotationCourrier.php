<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Numérotation du registre : A26-0001 (arrivée) et D26-0001 (départ), remise à zéro chaque année.
 *
 * prochain() DOIT être appelé dans la transaction qui crée le courrier : la ligne du compteur est verrouillée
 * jusqu'à la fin de la transaction, donc deux enregistrements simultanés n'obtiennent jamais le même numéro,
 * et un enregistrement annulé ne laisse pas de trou.
 */
class NumerotationCourrier
{
    /** @return array{0: string, 1: int} [numéro formaté, numéro d'ordre] */
    public static function prochain(string $sens, int $annee): array
    {
        DB::table('courrier_sequences')->insertOrIgnore(['sens' => $sens, 'annee' => $annee, 'dernier' => 0]);

        $ligne = DB::table('courrier_sequences')->where('sens', $sens)->where('annee', $annee)->lockForUpdate()->first();
        $suivant = $ligne->dernier + 1;
        DB::table('courrier_sequences')->where('id', $ligne->id)->update(['dernier' => $suivant]);

        $format = Reglages::get(match ($sens) {
            'depart' => 'courrier.format_depart',
            'diligence' => 'courrier.format_diligence',
            default => 'courrier.format_arrivee',
        });

        return [self::formater($format, $annee, $suivant), $suivant];
    }

    public static function formater(string $format, int $annee, int $numero): string
    {
        return preg_replace_callback('/\{(AAAA|AA|N{3,5})\}/', function ($m) use ($annee, $numero) {
            return match (true) {
                $m[1] === 'AAAA' => (string) $annee,
                $m[1] === 'AA' => substr((string) $annee, -2),
                default => str_pad((string) $numero, strlen($m[1]), '0', STR_PAD_LEFT),
            };
        }, $format);
    }
}
