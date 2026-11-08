<?php

namespace App\Support;

use App\Exceptions\ExerciceCloException;
use App\Models\Exercice;

/**
 * Verrou des exercices clos. Sans exercice clos, aucun coût (une seule requête par requête HTTP).
 *
 *   ExerciceVerrou::sansVerrou(fn () => ...);   // traitement système autorisé (maintenance)
 *   ExerciceVerrou::oublier();                  // après clôture / réouverture
 */
class ExerciceVerrou
{
    private static ?bool $existeClos = null;

    private static int $contournement = 0;

    /** @param  callable(): array<Exercice|null>  $exercices  évalué seulement si un exercice est clos */
    public static function verifier(callable $exercices): void
    {
        if (self::$contournement > 0) {
            return;
        }

        self::$existeClos ??= Exercice::whereNotNull('cloture_le')->exists();
        if (! self::$existeClos) {
            return;
        }

        foreach ($exercices() as $exercice) {
            if ($exercice && $exercice->estClos()) {
                throw new ExerciceCloException($exercice);
            }
        }
    }

    public static function sansVerrou(callable $action): mixed
    {
        self::$contournement++;
        try {
            return $action();
        } finally {
            self::$contournement--;
        }
    }

    public static function oublier(): void
    {
        self::$existeClos = null;
    }
}
