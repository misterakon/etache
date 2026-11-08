<?php

namespace App\Support;

use App\Models\ActionCle;
use App\Models\ActionMesurable;
use App\Models\Axe;
use App\Models\Exercice;
use App\Models\SituationTemporelle;
use App\Models\SousTache;
use App\Models\StatutExecution;
use App\Models\Tache;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Reconduction d'un exercice vers un autre. Jamais automatique : chaque élément est choisi.
 *
 * 1. reconduire() : tâches non terminées.
 *    - destination : l'action mesurable choisie pour la tâche ; à défaut, la structure d'origine (axe, action clé,
 *      action mesurable de même libellé) est reprise ou créée dans l'exercice de destination, si le réglage l'autorise ;
 *    - la nouvelle tâche repart à 0 %, « Non démarrée », avec l'échéance fournie, un nouveau code et un lien vers l'origine ;
 *    - la tâche source garde son état et est marquée « reconduite vers <exercice> » ;
 *    - les fichiers, justifications et historiques ne sont jamais repris.
 * 2. reconduireStructure() : axes, actions clés et actions mesurables seuls (éléments qui durent plusieurs années).
 *
 * Tout ou rien : une erreur annule l'ensemble de la reconduction.
 */
class ReconductionExercice
{
    /**
     * @param  array<int,string>  $echeances  id de tâche source => nouvelle échéance (Y-m-d)
     * @param  array<int,int|null>  $destinations  id de tâche source => id de l'action mesurable de destination (null = structure d'origine)
     * @return array{taches: int, axes: int, actions_cles: int, actions_mesurables: int, sous_taches: int}
     *
     * @throws DomainException
     */
    public static function reconduire(Exercice $source, Exercice $destination, array $echeances, array $destinations, bool $affectations, bool $sousTaches, bool $creerStructure, ?int $userId): array
    {
        $statutId = StatutExecution::where('code', StatutExecution::NON_DEMARREE)->value('id');
        $situationId = SituationTemporelle::where('code', SituationTemporelle::NORMAL)->value('id');

        return DB::transaction(function () use ($source, $destination, $echeances, $destinations, $affectations, $sousTaches, $creerStructure, $userId, $statutId, $situationId) {
            $stats = ['taches' => 0, 'axes' => 0, 'actions_cles' => 0, 'actions_mesurables' => 0, 'sous_taches' => 0];
            $axes = $acs = $ams = [];

            $taches = Tache::dansExercice($source->id)->nonTerminees()
                ->whereIn('id', array_keys($echeances))
                ->where(fn ($q) => $q->whereNull('reconduite_vers_exercice_id')->orWhere('reconduite_vers_exercice_id', '!=', $destination->id))
                ->with(['actionMesurable.actionCle.axe', 'affectations', 'sousTaches'])
                ->orderBy('code')
                ->get();

            foreach ($taches as $t) {
                $choix = $destinations[$t->id] ?? null;

                if ($choix) {
                    $amId = self::actionMesurableDeDestination((int) $choix, $destination);
                } elseif ($creerStructure) {
                    $am = $t->actionMesurable;
                    $ac = $am->actionCle;
                    $axeId = $axes[$ac->axe->id] ??= self::axe($ac->axe, $destination, $stats);
                    $acId = $acs[$ac->id] ??= self::actionCle($ac, $axeId, $affectations, $stats);
                    $amId = $ams[$am->id] ??= self::actionMesurable($am, $acId, $affectations, $stats);
                } else {
                    throw new DomainException("Choisissez l'action mesurable de destination de la tâche {$t->code}.");
                }

                $echeance = $echeances[$t->id];
                $debut = $destination->date_debut && $destination->date_debut->toDateString() <= $echeance ? $destination->date_debut->toDateString() : null;

                $nouvelle = Tache::create([
                    'action_mesurable_id' => $amId,
                    'libelle' => $t->libelle,
                    'indicateur' => $t->indicateur,
                    'livrable' => $t->livrable,
                    'ordre' => $t->ordre,
                    'date_debut' => $debut,
                    'date_fin' => $echeance,
                    'priorite_id' => $t->priorite_id,
                    'budget_estime' => $t->budget_estime,
                    'statut_execution_id' => $statutId,
                    'situation_temporelle_id' => $situationId,
                    'avancement_pourcentage' => 0,
                    'observations' => trim("Reconduite de {$t->code} ({$source->annee}). ".($t->observations ?? '')),
                    'derniere_mise_a_jour_le' => now(),
                    'reconduite_de_id' => $t->id,
                ]);

                if ($affectations) {
                    self::copierAffectations($t, $nouvelle);
                }
                if ($sousTaches) {
                    foreach ($t->sousTaches->where('cochee', false) as $s) {
                        SousTache::create(['tache_id' => $nouvelle->id, 'libelle' => $s->libelle, 'description' => $s->description, 'ordre' => $s->ordre, 'cree_par' => $userId]);
                        $stats['sous_taches']++;
                    }
                    AvancementTache::recalculer($nouvelle, $userId); // exercice « par sous-tâches » : aligne avancement et statut
                }

                // La source est dans un exercice (souvent clos) : requête directe, qui ne passe pas par le verrou
                DB::table('taches')->where('id', $t->id)->update(['reconduite_vers_exercice_id' => $destination->id]);

                Journal::noter('taches', 'reconduction', "Tâche créée par reconduction de {$t->code} ({$source->annee}) : {$nouvelle->code}", $nouvelle, $nouvelle, ['origine' => $t->code]);
                Journal::noter('taches', 'reconduction', "Tâche reconduite vers l'exercice {$destination->annee} : {$nouvelle->code}", $t, $t, ['nouvelle' => $nouvelle->code]);
                $stats['taches']++;
            }

            Journal::noter('exercices', 'reconduction', "Reconduction {$source->annee} → {$destination->annee} : {$stats['taches']} tâche(s)", $destination, null, $stats + ['source' => $source->annee]);

            return $stats;
        });
    }

    /**
     * Reprend la structure seule : axes, actions clés et actions mesurables choisis (leurs parents sont créés au besoin).
     *
     * @return array{axes: int, actions_cles: int, actions_mesurables: int}
     */
    public static function reconduireStructure(Exercice $source, Exercice $destination, array $axeIds, array $acIds, array $amIds, bool $affectations, ?int $userId): array
    {
        return DB::transaction(function () use ($source, $destination, $axeIds, $acIds, $amIds, $affectations) {
            $stats = ['taches' => 0, 'axes' => 0, 'actions_cles' => 0, 'actions_mesurables' => 0, 'sous_taches' => 0];
            $axes = $acs = [];

            $axeDest = function (Axe $a) use (&$axes, $destination, &$stats) {
                return $axes[$a->id] ??= self::axe($a, $destination, $stats);
            };
            $acDest = function (ActionCle $c) use (&$acs, $axeDest, $affectations, &$stats) {
                return $acs[$c->id] ??= self::actionCle($c, $axeDest($c->axe), $affectations, $stats);
            };

            $dansSource = fn ($q) => $q->where('exercice_id', $source->id);

            foreach (Axe::where('exercice_id', $source->id)->whereIn('id', $axeIds)->orderBy('ordre')->get() as $axe) {
                $axeDest($axe);
            }
            foreach (ActionCle::whereHas('axe', $dansSource)->whereIn('id', $acIds)->with('axe', 'directionsAssociees')->orderBy('ordre')->get() as $ac) {
                $acDest($ac);
            }
            foreach (ActionMesurable::whereHas('actionCle.axe', $dansSource)->whereIn('id', $amIds)->with('actionCle.axe', 'actionCle.directionsAssociees')->orderBy('ordre')->get() as $am) {
                self::actionMesurable($am, $acDest($am->actionCle), $affectations, $stats);
            }

            Journal::noter('exercices', 'reconduction', "Reconduction de la structure {$source->annee} → {$destination->annee} : {$stats['axes']} axe(s), {$stats['actions_cles']} action(s) clé(s), {$stats['actions_mesurables']} action(s) mesurable(s)", $destination, null, $stats + ['source' => $source->annee, 'structure_seule' => true]);

            return $stats;
        });
    }

    private static function actionMesurableDeDestination(int $id, Exercice $destination): int
    {
        $appartient = ActionMesurable::whereKey($id)
            ->whereHas('actionCle.axe', fn ($q) => $q->where('exercice_id', $destination->id))
            ->exists();

        if (! $appartient) {
            throw new DomainException("L'action mesurable choisie n'appartient pas à l'exercice {$destination->annee}.");
        }

        return $id;
    }

    private static function axe(Axe $axe, Exercice $destination, array &$stats): int
    {
        $nouveau = Axe::firstOrCreate(
            ['exercice_id' => $destination->id, 'libelle' => $axe->libelle],
            ['resultat_attendu' => $axe->resultat_attendu, 'description' => $axe->description, 'ordre' => $axe->ordre]
        );
        $nouveau->wasRecentlyCreated && $stats['axes']++;

        return $nouveau->id;
    }

    private static function actionCle(ActionCle $ac, int $axeId, bool $affectations, array &$stats): int
    {
        $nouvelle = ActionCle::firstOrCreate(
            ['axe_id' => $axeId, 'libelle' => $ac->libelle],
            ['direction_id' => $ac->direction_id, 'code' => $ac->code, 'description' => $ac->description, 'ordre' => $ac->ordre]
        );
        if ($nouvelle->wasRecentlyCreated) {
            $stats['actions_cles']++;
            $nouvelle->directionsAssociees()->sync($ac->directionsAssociees->pluck('id'));
            $affectations && self::copierAffectations($ac, $nouvelle);
        }

        return $nouvelle->id;
    }

    private static function actionMesurable(ActionMesurable $am, int $acId, bool $affectations, array &$stats): int
    {
        $nouvelle = ActionMesurable::firstOrCreate(
            ['action_cle_id' => $acId, 'libelle' => $am->libelle],
            ['description' => $am->description, 'ordre' => $am->ordre]
        );
        if ($nouvelle->wasRecentlyCreated) {
            $stats['actions_mesurables']++;
            $affectations && self::copierAffectations($am, $nouvelle);
        }

        return $nouvelle->id;
    }

    private static function copierAffectations(Model $origine, Model $nouveau): void
    {
        foreach ($origine->affectations as $a) {
            $nouveau->affectations()->create(['personnel_id' => $a->personnel_id, 'role_affectation_id' => $a->role_affectation_id]);
        }
    }
}
