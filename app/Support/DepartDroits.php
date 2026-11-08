<?php

namespace App\Support;

use App\Models\CourrierDepart;
use App\Models\User;

/** Droits du registre départ et de l'archivage. */
class DepartDroits
{
    public static function peutEnregistrer(?User $user): bool
    {
        return $user !== null && $user->hasRole(Reglages::get('courrier.roles_depart'));
    }

    public static function peutConsulter(?User $user): bool
    {
        return $user !== null && ($user->hasRole(Reglages::get('courrier.roles_depart')) || $user->hasRole(Reglages::get('courrier.roles_registre')));
    }

    public static function peutVoir(CourrierDepart $depart, ?User $user): bool
    {
        if (! self::peutConsulter($user)) {
            return false;
        }

        return ! $depart->estConfidentiel() || $user->hasRole('ADMIN|DG') || (int) $depart->enregistre_par === (int) $user->id;
    }

    public static function peutModifier(CourrierDepart $depart, ?User $user): bool
    {
        return self::peutEnregistrer($user) && self::peutVoir($depart, $user) && $depart->statut === 'enregistre';
    }

    /** Classer (archiver) un courrier ou un départ : service courrier, DG, ADMIN. */
    public static function peutClasser(?User $user): bool
    {
        return $user !== null && ($user->hasRole('ADMIN|DG') || self::peutEnregistrer($user) || CourrierDroits::peutEnregistrer($user));
    }
}
