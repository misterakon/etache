<?php

namespace App\Support;

use App\Models\Exercice;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\User;

/**
 * Avancement calculé à partir des sous-tâches (exercices en mode "sous_taches").
 *
 *  - avancement = sous-tâches cochées ÷ total × 100 (même poids), 0 si aucune sous-tâche ;
 *  - statut automatique (tâches ayant au moins une sous-tâche) : aucune cochée = Non démarrée,
 *    certaines = En cours, toutes = Terminée. Bloquée, Suspendue et Annulée ne sont jamais écrasées ;
 *  - une tâche SANS sous-tâche garde son statut ;
 *  - chaque changement est écrit dans tache_historiques (même format que JournalTache).
 *
 * En mode "manuel", ces méthodes ne font rien.
 */
class AvancementTache
{
    private const STATUTS_MANUELS = [StatutExecution::BLOQUEE, StatutExecution::SUSPENDUE, StatutExecution::ANNULEE];

    public static function pourcentage(int $total, int $cochees): int
    {
        return $total > 0 ? (int) round($cochees / $total * 100) : 0;
    }

    public static function recalculer(Tache $tache, ?int $userId = null): void
    {
        if (! $tache->exerciceTache()?->avancementParSousTaches()) {
            return;
        }

        $total = $tache->sousTaches()->validees()->count();
        $cochees = $tache->sousTaches()->validees()->where('cochee', true)->count();

        $champs = ['avancement_pourcentage' => self::pourcentage($total, $cochees)];

        $statutActuel = $tache->statutExecution()->value('code');
        $soumission = false;
        if ($total > 0 && Reglages::get('sous_taches.statut_automatique') && ! in_array($statutActuel, self::STATUTS_MANUELS, true)) {
            $code = match (true) {
                $cochees === 0 => StatutExecution::NON_DEMARREE,
                $cochees < $total => StatutExecution::EN_COURS,
                default => StatutExecution::TERMINEE,
            };

            // Validation de la clôture par le supérieur (Réglages > Clôture des tâches)
            if (ClotureTache::active()) {
                if ($code === StatutExecution::TERMINEE) {
                    if ($tache->cloture_validation === 'validee') {
                        // déjà validée : reste terminée
                    } elseif (ClotureTache::peutValider($tache, $userId ? User::find($userId) : null)) {
                        $champs += ClotureTache::champsValidation($userId);
                    } else {
                        $code = StatutExecution::EN_COURS;
                        $champs['avancement_pourcentage'] = min(99, $champs['avancement_pourcentage']);
                        if (! in_array($tache->cloture_validation, ['a_valider', 'refusee'], true)) {
                            $champs += ClotureTache::champsSoumission($userId);
                            $soumission = true;
                        }
                    }
                } elseif ($tache->cloture_validation !== null) {
                    $champs += ClotureTache::champsRetrait(); // une sous-tâche est repassée « à faire » : la clôture n'a plus d'objet
                }
            }
            $champs['statut_execution_id'] = StatutExecution::where('code', $code)->value('id');
        }

        $avant = JournalTache::instantane($tache);
        $tache->update($champs + ['derniere_mise_a_jour_le' => now()]);
        JournalTache::enregistrer($tache, $avant, $userId);
        $tache->unsetRelation('statutExecution');
        if ($soumission) {
            ClotureTache::apresSoumission($tache, $userId, 'dernière sous-tâche faite');
        }
    }

    /** Recalcule toutes les tâches d'un exercice (passage en mode "sous_taches"). Retourne leur nombre. */
    public static function recalculerExercice(Exercice $exercice, ?int $userId = null): int
    {
        $n = 0;
        Tache::dansExercice($exercice->id)
            ->with('actionMesurable.actionCle.axe.exercice')
            ->chunkById(100, function ($taches) use (&$n, $userId) {
                foreach ($taches as $tache) {
                    self::recalculer($tache, $userId);
                    $n++;
                }
            });

        return $n;
    }

    /** Aperçu d'impact avant le passage en mode "sous_taches". */
    public static function apercu(Exercice $exercice): array
    {
        $taches = Tache::dansExercice($exercice->id)
            ->withCount(['sousTaches' => fn ($q) => $q->validees(), 'sousTaches as sous_taches_cochees_count' => fn ($q) => $q->validees()->where('cochee', true)])
            ->get();

        $nouvelle = fn ($t) => self::pourcentage($t->sous_taches_count, $t->sous_taches_cochees_count);
        $changent = $taches->filter(fn ($t) => (int) $t->avancement_pourcentage !== $nouvelle($t));

        return [
            'total' => $taches->count(),
            'changent' => $changent->count(),
            'vers_zero' => $changent->filter(fn ($t) => $nouvelle($t) === 0)->count(),
        ];
    }
}
