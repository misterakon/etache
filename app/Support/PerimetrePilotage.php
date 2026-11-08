<?php

namespace App\Support;

use App\Models\User;

/** Périmètre d'un utilisateur dans le pilotage : l'ADMIN et le DG voient tout ; un responsable de direction, ses directions. */
class PerimetrePilotage
{
    /** @return list<int>|null null = toute la structure */
    public static function directions(User $user): ?array
    {
        if ($user->hasRole('ADMIN|DG')) {
            return null;
        }

        return $user->directionsDirigees()->all();
    }
}
