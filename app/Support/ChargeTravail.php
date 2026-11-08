<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\DiligenceAction;
use App\Models\Personnel;
use App\Models\StatutExecution;
use App\Models\Tache;
use Illuminate\Support\Collection;

/**
 * Charge de travail d'un agent : tâches ouvertes, retards, diligences du courrier et actions de traitement ouvertes.
 * Sur-affectation quand le total atteint le seuil des Réglages (Personnel).
 */
class ChargeTravail
{
    private static function tachesOuvertes(): Collection
    {
        return Tache::with(['statutExecution', 'affectations'])->get()
            ->reject(fn ($t) => in_array($t->statutExecution?->code, [StatutExecution::TERMINEE, StatutExecution::ANNULEE], true));
    }

    private static function ligne(Personnel $agent, Collection $taches, int $diligences, int $actions, int $seuil, int $ponctuelles = 0, int $ponctuellesRetard = 0): array
    {
        $charge = $taches->count() + $diligences + $actions + $ponctuelles;

        return [
            'personnel' => $agent,
            'taches' => $taches->count(),
            'retard' => $taches->filter(fn ($t) => $t->estEnRetard())->count() + $ponctuellesRetard,
            'ponctuelles' => $ponctuelles,
            'a_venir' => $taches->filter(fn ($t) => $t->date_fin && $t->date_fin->between(today(), today()->addDays(14)))->count(),
            'diligences' => $diligences,
            'actions' => $actions,
            'charge' => $charge,
            'seuil' => $seuil,
            'surcharge' => $charge >= $seuil,
        ];
    }

    /** @return Collection<int, array> tous les agents actifs, les plus chargés d'abord */
    public static function tous(): Collection
    {
        $seuil = (int) Reglages::get('personnel.seuil_surcharge');
        $parAgent = [];
        foreach (self::tachesOuvertes() as $tache) {
            foreach ($tache->affectations->pluck('personnel_id')->unique() as $pid) {
                $parAgent[$pid][] = $tache;
            }
        }
        $diligences = Diligence::ouvertes()->whereNotNull('responsable_personnel_id')->pluck('responsable_personnel_id')->countBy();
        $ponctuelles = \App\Models\TachePonctuelle::ouvertes()->pluck('responsable_personnel_id')->countBy();
        $ponctuellesRetard = \App\Models\TachePonctuelle::enRetard()->pluck('responsable_personnel_id')->countBy();
        $actions = DiligenceAction::validees()->where('faite', false)->whereNotNull('personnel_id')
            ->whereHas('diligence', fn ($d) => $d->ouvertes())->pluck('personnel_id')->countBy();

        return Personnel::nonArchives()->where('actif', true)->with('service:id,nom')->orderBy('nom')->get()
            ->map(fn ($a) => self::ligne($a, collect($parAgent[$a->id] ?? []), (int) ($diligences[$a->id] ?? 0), (int) ($actions[$a->id] ?? 0), $seuil, (int) ($ponctuelles[$a->id] ?? 0), (int) ($ponctuellesRetard[$a->id] ?? 0)))
            ->sortByDesc('charge')->values();
    }

    public static function pour(Personnel $agent): array
    {
        $taches = Tache::whereHas('affectations', fn ($a) => $a->where('personnel_id', $agent->id))->with('statutExecution')->get()
            ->reject(fn ($t) => in_array($t->statutExecution?->code, [StatutExecution::TERMINEE, StatutExecution::ANNULEE], true));
        $diligences = Diligence::ouvertes()->where('responsable_personnel_id', $agent->id)->count();
        $actions = DiligenceAction::validees()->where('faite', false)->where('personnel_id', $agent->id)
            ->whereHas('diligence', fn ($d) => $d->ouvertes())->count();

        return self::ligne($agent, $taches, $diligences, $actions, (int) Reglages::get('personnel.seuil_surcharge'),
            \App\Models\TachePonctuelle::ouvertes()->where('responsable_personnel_id', $agent->id)->count(), \App\Models\TachePonctuelle::enRetard()->where('responsable_personnel_id', $agent->id)->count());
    }
}
