<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\MandatResponsable;
use App\Notifications\DiligenceNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Alertes d'échéance proche et de retard des diligences. Deux déclencheurs, sans doublon (table diligence_alertes) :
 *  - la commande planifiée « courrier:alertes-diligences » (chaque matin) ;
 *  - un déclenchement « à la visite » (au plus toutes les 3 heures), au cas où le planificateur ne tournerait pas.
 */
class AlertesDiligences
{
    public static function executerSiNecessaire(): void
    {
        if (Cache::add('courrier.alertes_diligences', 1, now()->addHours(3))) {
            try {
                self::executer();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /** @return array{echeance: int, retard: int} nombre d'alertes émises */
    public static function executer(): array
    {
        $avant = (int) Reglages::get('courrier.alerte_jours_avant');
        $rappel = max(1, (int) Reglages::get('courrier.alerte_retard_rappel_jours'));
        $stats = ['echeance' => 0, 'retard' => 0];

        Diligence::ouvertes()->whereNotNull('responsable_personnel_id')->with(['courrier', 'responsable'])->get()
            ->each(function (Diligence $d) use ($avant, $rappel, &$stats) {
                $jours = (int) today()->diffInDays($d->echeance, false); // négatif : en retard

                if ($jours < 0) {
                    $retard = abs($jours);
                    if (! self::premiereFois($d, 'retard', $d->echeance->format('Ymd').'-r'.intdiv($retard - 1, $rappel))) {
                        return;
                    }
                    NotifDiligence::versPersonnel($d->responsable_personnel_id, $d, DiligenceNotification::RETARD, "Retard : {$retard} jour(s).");
                    $superieur = DestinatairesImputation::superieursDe($d->responsable)[0] ?? MandatResponsable::directeurGeneral();
                    if ($superieur && (int) $superieur->id !== (int) $d->responsable_personnel_id) {
                        NotifDiligence::versPersonnel($superieur->id, $d, DiligenceNotification::RETARD, "Responsable : {$d->responsable->nomComplet()} ({$retard} jour(s) de retard).");
                    }
                    $stats['retard']++;
                } elseif ($jours <= $avant && self::premiereFois($d, 'echeance', $d->echeance->toDateString())) {
                    NotifDiligence::versPersonnel($d->responsable_personnel_id, $d, DiligenceNotification::ECHEANCE);
                    $stats['echeance']++;
                }
            });

        return $stats;
    }

    /** Anti-doublon : vrai seulement la première fois pour (diligence, type, clé). */
    private static function premiereFois(Diligence $d, string $type, string $cle): bool
    {
        return DB::table('diligence_alertes')->insertOrIgnore(['diligence_id' => $d->id, 'type' => $type, 'cle' => $cle, 'envoye_le' => now()]) > 0;
    }
}
