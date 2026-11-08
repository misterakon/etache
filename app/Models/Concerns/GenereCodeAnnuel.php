<?php

namespace App\Models\Concerns;

/**
 * Code du type PREFIXE + 2 derniers chiffres de l'année + '-' + numéro séquentiel,
 * remis à zéro à chaque nouvelle année (ex: AM26-001, AM26-002... puis AM27-001 en 2027).
 *
 * Utilise l'année courante (now()) au moment de la création, en attendant la vraie
 * table `exercices` — à remplacer par exercice_id quand elle existera.
 *
 * Chaque modèle qui l'utilise définit :
 *   protected static string $prefixeCode = 'AM';
 *   protected static int $largeurCode = 3; // nombre de chiffres après le tiret
 */
trait GenereCodeAnnuel
{
    protected static function bootGenereCodeAnnuel(): void
    {
        static::creating(function ($modele) {
            if (empty($modele->code)) {
                $modele->code = static::prochainCode($modele->anneeCode());
            }
        });
    }

    /**
     * Année (2 chiffres) à utiliser pour le code. Par défaut, l'année du jour ;
     * à surcharger dans le modèle pour dériver la vraie valeur depuis l'exercice
     * (ex: via la relation actionCle->axe->exercice).
     */
    public function anneeCode(): string
    {
        return now()->format('y');
    }

    public static function prochainCode(?string $anneeCourte = null): string
    {
        $prefixe = static::$prefixeCode;
        $largeur = static::$largeurCode;
        $anneeCourte ??= now()->format('y');

        $motif = $prefixe.$anneeCourte.'-';

        $dernierNumero = static::query()
            ->where('code', 'like', $motif.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(code, ?) AS UNSIGNED)) as max_num', [mb_strlen($motif) + 1])
            ->value('max_num');

        $prochain = $dernierNumero ? ((int) $dernierNumero) + 1 : 1;

        return $motif.str_pad((string) $prochain, $largeur, '0', STR_PAD_LEFT);
    }
}
