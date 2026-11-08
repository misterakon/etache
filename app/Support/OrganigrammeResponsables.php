<?php

namespace App\Support;

use App\Models\Direction;
use App\Models\MandatResponsable;
use App\Models\Service;
use App\Models\SousDirection;
use Carbon\Carbon;

/**
 * Construit les lignes de l'ecran "Responsables" : toute la hierarchie, de la Direction Generale
 * aux services, avec le responsable en poste a une date (aujourd'hui par defaut).
 *
 * Etat d'une ligne : titulaire | interim | sans (aucun responsable) | inactif (le responsable en poste est un agent desactive)
 */
class OrganigrammeResponsables
{
    public static function construire(?Carbon $date = null): array
    {
        $date ??= today();

        // Mandats en vigueur : un seul par unite est retenu (l'interim d'abord, puis le plus recent)
        $parUnite = MandatResponsable::actifA($date)->prioritaire()->with('personnel')->get()
            ->groupBy(fn ($m) => self::cle($m->niveau, self::uniteId($m)))
            ->map->first();

        $directions = Direction::orderBy('nom')->get();
        $sousDirections = SousDirection::orderBy('nom')->get()->groupBy('direction_id');
        $services = Service::orderBy('nom')->get();

        $servicesDeSd = $services->whereNotNull('sous_direction_id')->groupBy('sous_direction_id');
        $servicesDeDirection = $services->whereNull('sous_direction_id')->whereNotNull('direction_id')->groupBy('direction_id');
        $servicesDg = $services->whereNull('sous_direction_id')->whereNull('direction_id')->values();

        $lignes = collect();
        $ajouter = function (string $niveau, ?int $uniteId, string $nom, ?string $code, int $profondeur, ?string $parent, string $ordre) use ($parUnite, $lignes) {
            $mandat = $parUnite->get(self::cle($niveau, $uniteId));
            $lignes->push([
                'ordre' => $ordre,               // cle de tri : garde l'ordre hierarchique dans le tableau
                'niveau' => $niveau,
                'unite_id' => $uniteId,
                'nom' => $nom,
                'code' => $code,
                'profondeur' => $profondeur,
                'parent' => $parent,
                'mandat' => $mandat,
                'responsable' => $mandat?->personnel,
                'etat' => match (true) {
                    ! $mandat => 'sans',
                    ! $mandat->personnel?->actif => 'inactif',
                    $mandat->type === MandatResponsable::INTERIM => 'interim',
                    default => 'titulaire',
                },
            ]);
        };

        // Direction Generale et services rattaches directement a elle
        $ajouter(MandatResponsable::NIVEAU_DG, null, 'Direction Générale', 'DG', 0, null, '000');
        foreach ($servicesDg as $i => $service) {
            $ajouter(MandatResponsable::NIVEAU_SERVICE, $service->id, $service->nom, $service->code, 1, 'Direction Générale', sprintf('000.%03d', $i + 1));
        }

        // Directions > sous-directions > services (+ services rattaches directement a la direction)
        foreach ($directions as $k => $direction) {
            $cleDirection = sprintf('%03d', $k + 1);
            $ajouter(MandatResponsable::NIVEAU_DIRECTION, $direction->id, $direction->nom, $direction->code, 1, 'Direction Générale', $cleDirection);

            foreach (($sousDirections->get($direction->id) ?? collect())->values() as $j => $sd) {
                $cleSd = $cleDirection.'.'.sprintf('%03d', $j + 1);
                $ajouter(MandatResponsable::NIVEAU_SOUS_DIRECTION, $sd->id, $sd->nom, $sd->code, 2, $direction->nom, $cleSd);

                foreach (($servicesDeSd->get($sd->id) ?? collect())->values() as $i => $service) {
                    $ajouter(MandatResponsable::NIVEAU_SERVICE, $service->id, $service->nom, $service->code, 3, $sd->nom, $cleSd.'.'.sprintf('%03d', $i + 1));
                }
            }

            foreach (($servicesDeDirection->get($direction->id) ?? collect())->values() as $i => $service) {
                $ajouter(MandatResponsable::NIVEAU_SERVICE, $service->id, $service->nom, $service->code, 2, $direction->nom, $cleDirection.'.'.sprintf('%03d', 500 + $i));
            }
        }

        $etats = $lignes->countBy('etat');

        return [
            'lignes' => $lignes->all(),
            'synthese' => [
                'unites' => $lignes->count(),
                'titulaires' => $etats['titulaire'] ?? 0,
                'interims' => $etats['interim'] ?? 0,
                'sans' => $etats['sans'] ?? 0,
                'inactifs' => $etats['inactif'] ?? 0,
            ],
        ];
    }

    private static function cle(string $niveau, ?int $uniteId): string
    {
        return $niveau.'|'.($uniteId ?? 0);
    }

    private static function uniteId(MandatResponsable $mandat): ?int
    {
        $colonne = MandatResponsable::cleUnite($mandat->niveau);

        return $colonne ? (int) $mandat->{$colonne} : null;
    }
}
