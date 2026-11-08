<?php

namespace App\Support;

use App\Models\Personnel;
use App\Models\TachePonctuelle;
use App\Models\User;
use App\Notifications\PonctuelleNotification;

/** Notifications des tâches ponctuelles : ne bloquent jamais l'action en cours. */
class NotifPonctuelle
{
    /** Vers les comptes actifs des agents donnés (hors auteur de l'action). */
    public static function versPersonnels(array $personnelIds, TachePonctuelle $t, string $evenement, ?string $detail = null, ?int $sauf = null): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $personnelIds))));
        if ($ids === []) {
            return;
        }
        try {
            foreach (User::where('actif', true)->whereIn('personnel_id', $ids)->get() as $u) {
                if ($sauf && (int) $u->id === $sauf) {
                    continue;
                }
                $u->notify(new PonctuelleNotification($t, $evenement, $detail));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Le supérieur direct de l'agent (premier maillon de la chaîne). */
    public static function superieurDirect(TachePonctuelle $t): ?int
    {
        return ($t->superieurs ?? [])[0] ?? null;
    }

    public static function agent(TachePonctuelle $t): ?Personnel
    {
        return $t->responsable;
    }
}
