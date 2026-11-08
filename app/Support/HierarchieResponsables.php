<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Chaine hierarchique d'un agent, utilisee par les alertes (escalade) et la justification de retard.
 */
class HierarchieResponsables
{
    /**
     * Responsables d'un agent, du plus proche au plus eleve.
     * Un mandat actif definit le niveau de depart pour un responsable ; sinon, la hierarchie
     * est deduite du service de l'agent. Les niveaux sans responsable sont ignores et l'agent
     * lui-meme n'apparait pas dans la chaine.
     */
    public static function chainePour(Personnel $agent, $date = null): Collection
    {
        $date = $date ? Carbon::parse($date) : today();
        $maillons = collect();

        $mandat = MandatResponsable::query()
            ->where('personnel_id', $agent->id)
            ->actifA($date)
            ->reorder()
            ->orderByRaw("CASE niveau WHEN 'service' THEN 0 WHEN 'sous_direction' THEN 1 WHEN 'direction' THEN 2 WHEN 'dga' THEN 3 WHEN 'direction_generale' THEN 4 ELSE 5 END")
            ->prioritaire()
            ->with(['service.sousDirection', 'service.directionDirecte', 'sousDirection.direction'])
            ->first();

        if ($mandat?->niveau === MandatResponsable::NIVEAU_SERVICE) {
            $service = $mandat->service;
            $sousDirection = $service?->sousDirection;

            if ($sousDirection) {
                $maillons->push($sousDirection->responsable($date));
            }

            $direction = $service?->direction();
            if ($direction) {
                $maillons->push($direction->responsable($date));
            }
        } elseif ($mandat?->niveau === MandatResponsable::NIVEAU_SOUS_DIRECTION) {
            $direction = $mandat->sousDirection?->direction;
            if ($direction) {
                $maillons->push($direction->responsable($date));
            }
        } elseif (! $mandat) {
            $service = $agent->service;
            if ($service) {
                $maillons->push($service->responsable($date));

                $sousDirection = $service->sousDirection;
                if ($sousDirection) {
                    $maillons->push($sousDirection->responsable($date));
                }

                $direction = $service->direction();
                if ($direction) {
                    $maillons->push($direction->responsable($date));
                }
            }
        }

        $maillons->push(MandatResponsable::directeurGeneral($date));

        return $maillons->filter()
            ->reject(fn ($responsable) => $responsable->id === $agent->id)
            ->unique('id')
            ->values();
    }

    /** Premier responsable au-dessus de l'agent (son superieur direct), ou null. */
    public static function superieurDirect(Personnel $agent, $date = null): ?Personnel
    {
        return self::chainePour($agent, $date)->first();
    }
}
