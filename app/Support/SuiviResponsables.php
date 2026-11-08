<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\User;
use App\Notifications\DiligenceNotification;
use Illuminate\Support\Facades\Cache;

/** Une diligence adressée à un poste ou une unité suit le titulaire (ou l'intérimaire) du jour. Recalcul à la visite, au plus toutes les 5 min. */
class SuiviResponsables
{
    public static function actualiser(bool $force = false): void
    {
        if (! config('etache.suivre_titulaire', true)) {
            return; // réglable : config/etache.php => 'suivre_titulaire' => false
        }
        if (! $force && ! Cache::add('courrier.suivi_responsables', 1, now()->addMinutes(5))) {
            return;
        }

        Diligence::actives()->where('destinataire_type', '!=', 'agent')->with('courrier')->get()->each(function (Diligence $d) {
            $cible = DestinatairesImputation::resoudre($d->destinataire_type, (int) $d->destinataire_id);
            $nouveau = $cible['responsable']?->id;
            if (! $cible || (int) $nouveau === (int) $d->responsable_personnel_id) {
                return;
            }
            $ancien = $d->responsable_personnel_id;
            $d->update(['responsable_personnel_id' => $nouveau]);
            Journal::noter('courrier', 'suivi_responsable', "Diligence {$d->code} : nouveau responsable en poste pour {$d->destinataire_libelle}", $d, $d->courrier, ['ancien' => $ancien, 'nouveau' => $nouveau]);
            self::notifier($d, $ancien, $nouveau);
        });
    }

    private static function notifier(Diligence $d, ?int $ancien, ?int $nouveau): void
    {
        try {
            foreach ([[$nouveau, DiligenceNotification::IMPUTATION], [$ancien, DiligenceNotification::INFORMATION]] as [$personnelId, $type]) {
                if (! $personnelId) {
                    continue;
                }
                User::where('actif', true)->where('personnel_id', $personnelId)->get()->each(fn ($u) => $u->notify(new DiligenceNotification($d, $type)));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
