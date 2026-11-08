<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\SousDirection;
use App\Models\TachePonctuelle;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Droits sur les tâches ponctuelles. La chaîne hiérarchique d'un agent (service, sous-direction, direction, DG)
 * voit ses tâches, peut les ajuster, les refuser ou les annuler ; l'agent crée les siennes, un responsable peut en confier.
 */
class PonctuellesDroits
{
    public static function voit(User $user, TachePonctuelle $t): bool
    {
        return $user->hasRole('ADMIN|DG')
            || (int) $t->cree_par_user_id === (int) $user->id
            || ($user->personnel_id && ((int) $t->responsable_personnel_id === (int) $user->personnel_id || in_array((int) $user->personnel_id, array_map('intval', $t->superieurs ?? []), true)));
    }

    public static function estResponsable(User $user, TachePonctuelle $t): bool
    {
        return $user->personnel_id && (int) $t->responsable_personnel_id === (int) $user->personnel_id;
    }

    /** Membre de la chaîne hiérarchique de l'agent (ou administrateur / DG) : il peut décider et ajuster. */
    public static function estSuperieur(User $user, TachePonctuelle $t): bool
    {
        return $user->hasRole('ADMIN|DG')
            || ($user->personnel_id && in_array((int) $user->personnel_id, array_map('intval', $t->superieurs ?? []), true));
    }

    /** L'agent concerné (ou le créateur) travaille sur la tâche ; la chaîne hiérarchique peut aussi la modifier. */
    public static function modifie(User $user, TachePonctuelle $t): bool
    {
        return self::estResponsable($user, $t) || (int) $t->cree_par_user_id === (int) $user->id || self::estSuperieur($user, $t);
    }

    /** Peut-on confier une tâche à cet agent ? Soi-même toujours ; un autre agent seulement s'il est dans sa chaîne de responsabilité. */
    public static function peutConfierA(User $user, Personnel $cible): bool
    {
        if ($user->personnel_id && (int) $user->personnel_id === (int) $cible->id) {
            return true;
        }
        if ($user->hasRole('ADMIN|DG')) {
            return true;
        }

        return $user->personnel_id && (self::agentsConfiables($user)->contains(fn ($p) => (int) $p->id === (int) $cible->id)
            || HierarchieResponsables::chainePour($cible)->contains(fn ($p) => (int) $p->id === (int) $user->personnel_id));
    }

    /** Agents auxquels l'utilisateur peut confier une tâche : lui-même, et les agents placés sous sa responsabilité. */
    public static function agentsConfiables(User $user): Collection
    {
        $moi = $user->personnel;
        $liste = collect();
        if ($user->hasRole('ADMIN|DG')) {
            $liste = Personnel::query()->where('actif', true)->whereNull('archive_le')->orderBy('nom')->get();
        } elseif ($moi) {
            $services = collect();
            $tous = false;
            $mandats = MandatResponsable::where('personnel_id', $moi->id)->actifA()->get();
            foreach ($mandats as $m) {
                if (in_array($m->niveau, [MandatResponsable::NIVEAU_DG, MandatResponsable::NIVEAU_DGA], true)) {
                    $tous = true;
                } elseif ($m->niveau === MandatResponsable::NIVEAU_SERVICE && $m->service_id) {
                    $services->push((int) $m->service_id);
                } elseif ($m->niveau === MandatResponsable::NIVEAU_SOUS_DIRECTION && $m->sous_direction_id) {
                    $services = $services->merge(Service::where('sous_direction_id', $m->sous_direction_id)->pluck('id'));
                } elseif ($m->niveau === MandatResponsable::NIVEAU_DIRECTION && $m->direction_id) {
                    $sd = SousDirection::where('direction_id', $m->direction_id)->pluck('id');
                    $services = $services->merge(Service::where('direction_id', $m->direction_id)->orWhereIn('sous_direction_id', $sd)->pluck('id'));
                }
            }
            if ($tous) {
                $liste = Personnel::query()->where('actif', true)->whereNull('archive_le')->orderBy('nom')->get();
            } elseif ($services->isNotEmpty()) {
                $liste = Personnel::query()->where('actif', true)->whereNull('archive_le')->whereIn('service_id', $services->unique())->orderBy('nom')->get();
            }
            $liste = $liste->reject(fn ($p) => (int) $p->id === (int) $moi->id)->prepend($moi);
        }

        return $liste->unique('id')->values();
    }

    /** Ids des responsables de la chaîne de l'agent (service, sous-direction, direction, DG), pour la visibilité. */
    public static function chaineIds(Personnel $agent): array
    {
        return HierarchieResponsables::chainePour($agent)->pluck('id')->map(fn ($i) => (int) $i)->values()->all();
    }

    /** L'utilisateur peut-il superviser (a-t-il au moins un subordonné) : sert à afficher l'onglet « Mon équipe ». */
    public static function supervise(User $user): bool
    {
        return $user->hasRole('ADMIN|DG') || ($user->personnel_id && TachePonctuelle::query()->whereJsonContains('superieurs', (int) $user->personnel_id)->exists())
            || $user->directionsDirigees()->isNotEmpty() || self::agentsConfiables($user)->count() > 1;
    }
}
