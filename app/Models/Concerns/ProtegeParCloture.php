<?php

namespace App\Models\Concerns;

use App\Support\ExerciceVerrou;

/**
 * Refuse toute création, modification ou suppression d'un enregistrement rattaché à un exercice clos.
 * Le modèle indique son ou ses exercices dans exercicesProteges().
 *
 * Contrôle côté serveur, au niveau du modèle : il couvre formulaires, import et appels internes.
 * Il ne couvre pas les requêtes de masse (query builder, DB::table) : celles-ci restent à la charge du code qui les écrit.
 */
trait ProtegeParCloture
{
    public static function bootProtegeParCloture(): void
    {
        foreach (['creating', 'updating', 'deleting'] as $evenement) {
            static::$evenement(fn ($modele) => ExerciceVerrou::verifier(fn () => $modele->exercicesProteges()));
        }
    }

    /** @return array<\App\Models\Exercice|null> */
    abstract public function exercicesProteges(): array;
}
