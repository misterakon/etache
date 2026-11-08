<?php

namespace App\Models\Concerns;

/**
 * A appliquer aux modèles "paramètre" dont le code est une séquence numérique
 * formatée sur plusieurs chiffres (ex: '001', '002'...), comme les seeders l'ont fait.
 *
 * Ne s'applique PAS à RegleAlerte, dont les codes sont des identifiants métier
 * (ex: 'J-30', 'J+7'), pas une séquence.
 */
trait GenereCodeSequentiel
{
    protected static function bootGenereCodeSequentiel(): void
    {
        static::creating(function ($modele) {
            if (empty($modele->code)) {
                $modele->code = static::prochainCode();
            }
        });
    }

    /**
     * Calcule le prochain code disponible en se basant sur le plus grand
     * code numérique déjà présent, quel que soit l'ordre de création/suppression.
     */
    public static function prochainCode(int $largeur = 3): string
    {
        $dernier = static::query()
            ->selectRaw('MAX(CAST(code AS UNSIGNED)) as max_code')
            ->value('max_code');

        $prochain = $dernier ? ((int) $dernier) + 1 : 1;

        return str_pad((string) $prochain, $largeur, '0', STR_PAD_LEFT);
    }
}
