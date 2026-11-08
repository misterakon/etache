<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\RoleAffectation;
use App\Models\SousTache;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\User;

/**
 * Qui peut créer, modifier, cocher ou supprimer les sous-tâches d'une tâche.
 *  - profils des Réglages : toutes les tâches ; sinon, le responsable de la tâche ;
 *  - tâche annulée ou exercice clos : sous-tâches figées pour tous ;
 *  - après l'échéance : seuls les profils des Réglages.
 * Les autres (pilote, contributeurs...) consultent.
 */
class SousTacheDroits
{
    /** @return array{gerer: bool, raison: ?string} */
    public static function pour(Tache $tache, ?User $user): array
    {
        $raison = self::raison($tache, $user);

        return ['gerer' => $raison === null, 'raison' => $raison];
    }

    /** L'utilisateur est-il responsable de la tâche (via sa fiche agent) ? */
    public static function estResponsable(Tache $tache, ?User $user): bool
    {
        return (bool) ($user?->personnel_id && $tache->affectations()
            ->where('personnel_id', $user->personnel_id)
            ->whereIn('role_affectation_id', RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->select('id'))
            ->exists());
    }

    /** Supérieurs directs des responsables de la tâche (chaîne : chef de service, sous-directeur, directeur, DG). */
    public static function validateurs(Tache $tache)
    {
        $ids = $tache->affectations()
            ->whereIn('role_affectation_id', RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->select('id'))
            ->pluck('personnel_id');

        return Personnel::whereIn('id', $ids)->get()
            ->map(fn ($p) => ChaineHierarchique::auDessus($p)[0]['personnel'] ?? MandatResponsable::directeurGeneral())
            ->filter()->unique('id')->values();
    }

    /** Valider ou refuser une sous-tâche : ADMIN, DG, ou supérieur hiérarchique direct du responsable de la tâche. */
    public static function peutValider(Tache $tache, ?User $user): bool
    {
        return $user !== null && ($user->hasRole('ADMIN|DG')
            || ($user->personnel_id && self::validateurs($tache)->contains('id', (int) $user->personnel_id)));
    }

    /** Cocher : sous-tâche validée, par le responsable (droits de gestion) ou par l'agent concerné. */
    public static function peutCocher(SousTache $sous, ?User $user): bool
    {
        if (! $user || ! $sous->estValidee()) {
            return false;
        }
        $tache = $sous->tache;
        $exercice = $tache->exerciceTache();
        if (($exercice && $exercice->estClos()) || $tache->statutExecution()->value('code') === StatutExecution::ANNULEE) {
            return false;
        }

        return self::pour($tache, $user)['gerer'] || ($user->personnel_id && (int) $sous->personnel_id === (int) $user->personnel_id);
    }

    private static function raison(Tache $tache, ?User $user): ?string
    {
        if (! $user) {
            return 'Connexion requise.';
        }

        if ($exercice = $tache->exerciceTache()) {
            if ($exercice->estClos()) {
                return "Exercice {$exercice->annee} clos : les sous-tâches sont figées.";
            }
        }
        if (! $user->hasRole(Reglages::get('sous_taches.roles_gestion')) && ! self::estResponsable($tache, $user)) {
            return 'Seul le responsable de la tâche peut gérer ses sous-tâches.';
        }
        if ($tache->statutExecution()->value('code') === StatutExecution::ANNULEE) {
            return 'Tâche annulée : les sous-tâches sont figées.';
        }
        if ($tache->date_fin && $tache->date_fin->isBefore(today()) && ! $user->hasRole(Reglages::get('sous_taches.roles_apres_echeance'))) {
            return 'Échéance dépassée : seuls les profils autorisés dans les Réglages peuvent modifier les sous-tâches.';
        }

        return null;
    }
}
