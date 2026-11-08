<?php

namespace App\Support;

use App\Models\SousTache;
use App\Models\User;
use App\Notifications\SousTacheNotification;

/** Notifications du circuit de validation des sous-tâches : ne bloque jamais l'action en cours. */
class NotifSousTache
{
    public static function versPersonnel(?int $personnelId, SousTache $sous, string $evenement, ?string $detail = null, ?int $sauf = null): void
    {
        if ($personnelId) {
            self::envoyer(User::where('actif', true)->where('personnel_id', $personnelId)->get(), $sous, $evenement, $detail, $sauf);
        }
    }

    public static function versUtilisateur(?int $userId, SousTache $sous, string $evenement, ?string $detail = null, ?int $sauf = null): void
    {
        if ($userId) {
            self::envoyer(User::where('actif', true)->where('id', $userId)->get(), $sous, $evenement, $detail, $sauf);
        }
    }

    private static function envoyer($utilisateurs, SousTache $sous, string $evenement, ?string $detail, ?int $sauf): void
    {
        try {
            foreach ($utilisateurs as $u) {
                if ($sauf && (int) $u->id === $sauf) {
                    continue;
                }
                $u->notify(new SousTacheNotification($sous, $evenement, $detail));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
