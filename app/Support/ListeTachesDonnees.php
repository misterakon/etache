<?php

namespace App\Support;

use App\Models\StatutExecution;
use Illuminate\Support\Collection;

/**
 * Prépare les données de la vue "Liste des tâches" :
 * compteurs de la vue d'ensemble, onglets En cours / En attente / Terminées,
 * onglet Diligence (à venir + en retard) et couleur de chaque carte.
 *
 * La vue Blade ne fait plus que de l'affichage.
 */
class ListeTachesDonnees
{
    /** Nombre de jours pris en compte pour les tâches "à venir" (aujourd'hui inclus). */
    private const JOURS_A_VENIR = 7;

    /**
     * @param  Collection  $taches  Tâches avec leurs relations chargées
     *                              (statutExecution, affectations.personnel)
     */
    public static function depuis(Collection $taches): array
    {
        $code = fn ($t) => $t->statutExecution?->code;
        $parCodes = fn (array $codes) => $taches
            ->filter(fn ($t) => in_array($code($t), $codes, true))
            ->values();

        $encours = $parCodes([StatutExecution::EN_COURS]);
        $attente = $parCodes([StatutExecution::NON_DEMARREE, StatutExecution::BLOQUEE, StatutExecution::SUSPENDUE]);
        $terminees = $parCodes([StatutExecution::TERMINEE]);

        $total = $taches->count();

        return [
            // --- Vue d'ensemble ---
            'encours' => $encours,
            'attente' => $attente,
            'terminees' => $terminees,
            'total' => $total,
            'avancementMoyen' => $total ? (int) round($taches->avg('avancement_pourcentage')) : 0,
            'tauxRealisation' => $total ? (int) round($terminees->count() / $total * 100) : 0,
            'nbRetard' => $taches->filter(fn ($t) => $t->estEnRetard())->count(),
            'budgetTotal' => (float) $taches->sum('budget_estime'),
            'budgetTermine' => (float) $terminees->sum('budget_estime'),
            'agents' => $taches->flatMap(fn ($t) => $t->affectations)->pluck('personnel')->filter()->unique('id')->values(),

            // --- Onglets 2 à 4 : [titre, ton (voir tache-list.css), tâches, icône] ---
            'onglets' => [
                2 => ['En cours', 'encours', $encours, 'fa-solid fa-bars-progress'],
                3 => ['En attente', 'attente', $attente, 'fa-regular fa-hourglass-half'],
                4 => ['Terminées', 'terminee', $terminees, 'fa-solid fa-circle-check'],
            ],

            // --- Onglet 1 "Tout" : couleur de chaque carte selon le statut (clé = id de la tâche) ---
            'tons' => $taches->mapWithKeys(fn ($t) => [
                $t->id => match ($code($t)) {
                    StatutExecution::TERMINEE => 'terminee',
                    StatutExecution::EN_COURS => 'encours',
                    StatutExecution::ANNULEE => 'neutre',
                    default => 'attente',
                },
            ])->all(),
        ] + self::diligence($taches, $code);
    }

    /** Onglet 5 "Diligence" : tâches à venir (aujourd'hui + 7 jours), puis tâches en retard. */
    private static function diligence(Collection $taches, \Closure $code): array
    {
        $finPeriode = today()->addDays(self::JOURS_A_VENIR)->endOfDay();

        // Tâches closes (terminées ou annulées) : jamais "à venir" ni "en retard"
        $codesClos = [StatutExecution::TERMINEE, StatutExecution::ANNULEE];
        $nonTerminees = $taches->filter(
            fn ($t) => ! in_array($code($t), $codesClos, true) && $t->date_fin
        );

        $parEcheance = fn (Collection $liste) => $liste
            ->sortBy(fn ($t) => $t->date_fin->timestamp)
            ->values();

        $retards = $parEcheance($nonTerminees->filter(fn ($t) => $t->estEnRetard()));
        $avenir = $parEcheance($nonTerminees->filter(
            fn ($t) => ! $t->estEnRetard()
                && $t->date_fin->copy()->startOfDay()->between(today(), $finPeriode)
        ));

        return [
            'retards' => $retards,
            'avenir' => $avenir,
            'nbDiligence' => $retards->count() + $avenir->count(),
            // [titre, couleur, tâches, message si vide, icône]
            'diligence' => [
                ["Tâches à venir (aujourd'hui et 7 prochains jours)", 'avenir', $avenir, 'Aucune échéance dans les 7 prochains jours.', 'fa-regular fa-calendar-check'],
                ['Tâches en retard', 'retard', $retards, 'Aucune tâche en retard, bravo !', 'fa-solid fa-triangle-exclamation'],
            ],
        ];
    }
}
