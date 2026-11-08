<?php

namespace App\Models\Concerns;

/**
 * Variante de GenereCodeSequentiel avec un préfixe fixe (A, AM, TA...).
 * Chaque modèle qui l'utilise définit :
 *   protected static string $prefixeCode = 'A';
 *   protected static int $largeurCode = 3; // nombre de chiffres après le préfixe
 */
trait GenereCodeAvecPrefixe
{
    protected static function bootGenereCodeAvecPrefixe(): void
    {
        static::creating(function ($modele) {
            if (empty($modele->code)) {
                $modele->code = static::prochainCode();
            }
        });
    }

    public static function prochainCode(): string
    {
        $prefixe = static::$prefixeCode;
        $largeur = static::$largeurCode;

        $dernierNumero = static::query()
            ->where('code', 'like', $prefixe.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(code, ?) AS UNSIGNED)) as max_num', [mb_strlen($prefixe) + 1])
            ->value('max_num');

        $prochain = $dernierNumero ? ((int) $dernierNumero) + 1 : 1;

        return $prefixe.str_pad((string) $prochain, $largeur, '0', STR_PAD_LEFT);
    }
}
