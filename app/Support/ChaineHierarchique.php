<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;

/** Chaîne hiérarchique montante d'un agent : chef de service, sous-directeur, directeur, puis Directeur Général. */
class ChaineHierarchique
{
    /** @return list<array{personnel: Personnel, libelle: string}> du supérieur le plus proche jusqu'au DG */
    public static function auDessus(Personnel $agent): array
    {
        $service = $agent->service;
        $sousDirection = $service?->sousDirection;
        $direction = $service?->direction();

        foreach ($agent->mandats()->actifA()->with(['direction', 'sousDirection', 'service'])->get() as $mandat) {
            match ($mandat->niveau) {
                MandatResponsable::NIVEAU_SERVICE => $service ??= $mandat->service,
                MandatResponsable::NIVEAU_SOUS_DIRECTION => $sousDirection ??= $mandat->sousDirection,
                MandatResponsable::NIVEAU_DIRECTION => $direction ??= $mandat->direction,
                default => null,
            };
        }
        $sousDirection ??= $service?->sousDirection;
        $direction ??= $sousDirection?->direction ?? $service?->direction();

        $chaine = [];
        $ajouter = function (?Personnel $p, string $libelle) use (&$chaine, $agent) {
            if ($p && $p->actif && $p->id !== $agent->id && ! collect($chaine)->contains(fn ($c) => $c['personnel']->id === $p->id)) {
                $chaine[] = ['personnel' => $p, 'libelle' => $libelle];
            }
        };

        $ajouter($service?->responsable(today()), 'Chef de service — '.$service?->nom);
        $ajouter($sousDirection?->responsable(today()), 'Sous-directeur — '.$sousDirection?->nom);
        $ajouter($direction?->responsable(today()), 'Directeur — '.$direction?->nom);
        $ajouter(MandatResponsable::directeurGeneral(), 'Directeur Général');

        return $chaine;
    }
}
