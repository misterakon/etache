<?php

namespace App\Support;

use App\Models\Direction;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\PosteImputation;
use App\Models\Service;
use App\Models\SousDirection;
use App\Models\User;

/**
 * Destinataires possibles d'une imputation : une unité de l'organisation, un poste (DGA, chargé d'études...) ou un agent.
 * Un destinataire s'écrit « type:id » (direction:3, service:12, poste:1, agent:57).
 */
class DestinatairesImputation
{
    public const TYPES = ['direction', 'sous_direction', 'service', 'poste', 'agent'];

    /** Options du formulaire d'imputation. */
    public static function groupes(): array
    {
        return [
            'directions' => Direction::orderBy('nom')->get(['id', 'nom']),
            'postes' => PosteImputation::where('actif', true)->whereNotNull('niveau')->orderBy('ordre')->get(),
            'services_dg' => Service::whereNull('direction_id')->whereNull('sous_direction_id')->orderBy('nom')->get(['id', 'nom']),
            'sous_directions' => SousDirection::with('direction:id,nom')->orderBy('nom')->get(),
            'services' => Service::where(fn ($q) => $q->whereNotNull('direction_id')->orWhereNotNull('sous_direction_id'))->orderBy('nom')->get(['id', 'nom']),
            'agents' => Personnel::where('actif', true)->with('service:id,nom')->orderBy('nom')->orderBy('prenom')->get(),
        ];
    }

    /**
     * Libellé et responsable en poste d'un destinataire.
     *
     * @return array{libelle: string, responsable: ?Personnel}|null null si le destinataire n'existe pas
     */
    public static function resoudre(string $type, int $id): ?array
    {
        $aujourdhui = today();

        switch ($type) {
            case 'direction':
            case 'sous_direction':
            case 'service':
                $unite = match ($type) {
                    'direction' => Direction::find($id),
                    'sous_direction' => SousDirection::find($id),
                    default => Service::find($id),
                };

                return $unite ? ['libelle' => $unite->nom, 'responsable' => method_exists($unite, 'responsable') ? $unite->responsable($aujourdhui) : null] : null;

            case 'poste':
                $poste = PosteImputation::with('personnel')->find($id);

                return $poste ? [
                    'libelle' => $poste->libelleActuel(),
                    'responsable' => $poste->niveau ? $poste->titulaireActuel() : $poste->personnel,
                ] : null;

            case 'agent':
                $agent = Personnel::find($id);

                return $agent ? ['libelle' => $agent->nomComplet(), 'responsable' => $agent] : null;
        }

        return null;
    }

    /** Destinations de poste qui sont actuellement occupées par cet agent. */
    public static function clesAssocieesAuPersonnel(int $personnelId): array
    {
        $agent = Personnel::find($personnelId);
        if (! $agent) {
            return [];
        }

        $cles = ['agent:'.$personnelId];
        foreach (PosteImputation::where('actif', true)->whereNotNull('niveau')->get() as $poste) {
            if ((int) ($poste->titulaireActuel()?->id ?? 0) === $personnelId) {
                $cles[] = 'poste:'.$poste->id;
            }
        }

        return array_values(array_unique($cles));
    }

    /** Positions de l'organigramme configurables comme destinataires d'imputation. */
    public static function postesOrganigramme(): array
    {
        $positions = [
            ['niveau' => MandatResponsable::NIVEAU_DG, 'type' => null, 'id' => null, 'libelle' => 'Directeur Général'],
            ['niveau' => MandatResponsable::NIVEAU_DGA, 'type' => null, 'id' => null, 'libelle' => 'Directeur Général Adjoint'],
        ];

        foreach (Direction::orderBy('nom')->get(['id', 'nom']) as $direction) {
            $positions[] = ['niveau' => MandatResponsable::NIVEAU_DIRECTION, 'type' => 'direction', 'id' => $direction->id, 'libelle' => 'Directeur — '.$direction->nom];
        }
        foreach (SousDirection::with('direction:id,nom')->orderBy('nom')->get() as $sousDirection) {
            $positions[] = ['niveau' => MandatResponsable::NIVEAU_SOUS_DIRECTION, 'type' => 'sous_direction', 'id' => $sousDirection->id, 'libelle' => 'Sous-directeur — '.$sousDirection->nom];
        }
        foreach (Service::with(['sousDirection:id,nom', 'directionDirecte:id,nom'])->orderBy('nom')->get() as $service) {
            $positions[] = ['niveau' => MandatResponsable::NIVEAU_SERVICE, 'type' => 'service', 'id' => $service->id, 'libelle' => 'Chef de service — '.$service->nom];
        }

        $configurations = PosteImputation::whereNotNull('niveau')->get()->keyBy(fn ($poste) => $poste->niveau.'|'.($poste->unite_type ?? '').'|'.($poste->unite_id ?? ''));

        return collect($positions)->map(function (array $position) use ($configurations): array {
            $cle = $position['niveau'].'|'.($position['type'] ?? '').'|'.($position['id'] ?? '');
            $configuration = $configurations->get($cle);
            $mandat = $position['niveau'] === MandatResponsable::NIVEAU_DG
                ? null
                : MandatResponsable::enPoste($position['niveau'], $position['id']);
            $titulaire = $position['niveau'] === MandatResponsable::NIVEAU_DG
                ? MandatResponsable::directeurGeneral()
                : $mandat?->personnel;

            return $position + [
                'cle' => $position['niveau'].':'.($position['id'] ?? 0),
                'configuration' => $configuration,
                'titulaire' => $titulaire,
                'interimaire' => $mandat?->type === MandatResponsable::INTERIM,
            ];
        })->values()->all();
    }

    /** Supérieurs hiérarchiques d'un agent : responsable de son service, puis de sa direction (hors lui-même). */
    public static function superieursDe(Personnel $agent): array
    {
        $service = $agent->service;
        $candidats = [
            $service && method_exists($service, 'responsable') ? $service->responsable(today()) : null,
            $service?->direction() && method_exists($service->direction(), 'responsable') ? $service->direction()->responsable(today()) : null,
        ];

        return collect($candidats)->filter(fn ($p) => $p && $p->id !== $agent->id)->unique('id')->values()->all();
    }

    /** Groupes de destinataires que cet utilisateur peut viser en réimputant : uniquement sous lui. */
    public static function groupesPour(User $user): array
    {
        $g = self::groupes();
        if ($user->hasRole('ADMIN|DG')) {
            return $g;
        }
        $mandats = MandatResponsable::where('personnel_id', $user->personnel_id)->actifA()->get();
        if ($mandats->contains('niveau', MandatResponsable::NIVEAU_DGA)) {
            $g['agents'] = $g['agents']->where('id', '!=', $user->personnel_id);

            return $g; // le DGA voit tout l'organigramme sous la DG
        }

        $dirs = $mandats->where('niveau', MandatResponsable::NIVEAU_DIRECTION)->pluck('direction_id')->all();
        $sds = $mandats->where('niveau', MandatResponsable::NIVEAU_SOUS_DIRECTION)->pluck('sous_direction_id')->all();
        $svs = $mandats->where('niveau', MandatResponsable::NIVEAU_SERVICE)->pluck('service_id')->all();

        $sdCibles = SousDirection::whereIn('direction_id', $dirs)->pluck('id')->map(fn ($i) => (int) $i)->all();
        $svCibles = Service::whereIn('direction_id', $dirs)->orWhereIn('sous_direction_id', array_merge($sdCibles, $sds))->pluck('id')->map(fn ($i) => (int) $i)->all();
        $svAgents = array_merge($svCibles, array_map('intval', $svs));

        $g['directions'] = collect();
        $g['services_dg'] = collect();
        $g['sous_directions'] = $g['sous_directions']->whereIn('id', $sdCibles);
        $g['services'] = $g['services']->whereIn('id', $svCibles);
        $g['agents'] = $g['agents']->whereIn('service_id', $svAgents)->where('id', '!=', $user->personnel_id);
        $g['postes'] = $g['postes']->filter(fn ($p) => ($p->niveau === MandatResponsable::NIVEAU_SOUS_DIRECTION && in_array((int) $p->unite_id, $sdCibles, true))
            || ($p->niveau === MandatResponsable::NIVEAU_SERVICE && in_array((int) $p->unite_id, $svCibles, true)))->values();

        return $g;
    }

    /** Clés « type:id » autorisées par un jeu de groupes (contrôle côté serveur). */
    public static function cles(array $g): array
    {
        return collect($g['directions'])->map(fn ($x) => 'direction:'.$x->id)
            ->merge(collect($g['sous_directions'])->map(fn ($x) => 'sous_direction:'.$x->id))
            ->merge(collect($g['services_dg'])->concat($g['services'])->map(fn ($x) => 'service:'.$x->id))
            ->merge(collect($g['postes'])->map(fn ($x) => 'poste:'.$x->id))
            ->merge(collect($g['agents'])->map(fn ($x) => 'agent:'.$x->id))->all();
    }
}
