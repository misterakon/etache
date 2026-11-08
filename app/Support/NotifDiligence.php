<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\User;
use App\Notifications\DiligenceNotification;

/** Envoi des notifications du courrier : ne bloque jamais l'action en cours. */
class NotifDiligence
{
    /** Tous les comptes actifs liés à une fiche agent. */
    public static function versPersonnel(?int $personnelId, Diligence $diligence, string $evenement, ?string $detail = null, ?int $sauf = null): int
    {
        if (! $personnelId) {
            return 0;
        }

        return self::envoyer(User::where('actif', true)->where('personnel_id', $personnelId)->get(), $diligence, $evenement, $detail, $sauf);
    }

    public static function versUtilisateur(?int $userId, Diligence $diligence, string $evenement, ?string $detail = null, ?int $sauf = null): int
    {
        if (! $userId) {
            return 0;
        }

        return self::envoyer(User::where('actif', true)->where('id', $userId)->get(), $diligence, $evenement, $detail, $sauf);
    }

    private static function envoyer($utilisateurs, Diligence $diligence, string $evenement, ?string $detail, ?int $sauf): int
    {
        $n = 0;
        try {
            foreach ($utilisateurs as $utilisateur) {
                if ($sauf && (int) $utilisateur->id === $sauf) {
                    continue;
                }
                $utilisateur->notify(new DiligenceNotification($diligence, $evenement, $detail));
                $n++;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $n;
    }
}
