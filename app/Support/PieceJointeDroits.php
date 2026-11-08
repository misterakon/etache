<?php

namespace App\Support;

use App\Models\PieceJointe;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\User;

/**
 * Droits sur les fichiers d'une tâche (et de ses sous-tâches).
 *  - voir / télécharger : toute personne qui voit la tâche ;
 *  - déposer : profils des Réglages et responsable de la tâche ; tâche annulée ou exercice clos = fichiers figés ;
 *  - supprimer : profils des Réglages ; l'auteur avant l'échéance si le réglage l'autorise.
 */
class PieceJointeDroits
{
    public static function voir(Tache $tache, ?User $user): bool
    {
        return $user !== null && Tache::visiblePar($user)->whereKey($tache->id)->exists();
    }

    /** @return array{ok: bool, raison: ?string} */
    public static function deposer(Tache $tache, ?User $user): array
    {
        if (! $user) {
            return ['ok' => false, 'raison' => 'Connexion requise.'];
        }
        if ($tache->exerciceTache()?->estClos()) {
            return ['ok' => false, 'raison' => 'Exercice clos : les fichiers sont figés.'];
        }
        if ($tache->statutExecution()->value('code') === StatutExecution::ANNULEE) {
            return ['ok' => false, 'raison' => 'Tâche annulée : les fichiers sont figés.'];
        }
        if (! $user->hasRole(Reglages::get('fichiers.roles_depot')) && ! SousTacheDroits::estResponsable($tache, $user)) {
            return ['ok' => false, 'raison' => 'Seul le responsable de la tâche peut déposer des fichiers.'];
        }

        return ['ok' => true, 'raison' => null];
    }

    public static function supprimer(PieceJointe $piece, Tache $tache, ?User $user): bool
    {
        if (! $user || $tache->exerciceTache()?->estClos() || $tache->statutExecution()->value('code') === StatutExecution::ANNULEE) {
            return false;
        }
        if ($user->hasRole(Reglages::get('fichiers.roles_suppression'))) {
            return true;
        }

        $avantEcheance = ! $tache->date_fin || ! $tache->date_fin->isBefore(today());

        return Reglages::get('fichiers.auteur_supprime_avant_echeance') && $piece->depose_par === $user->id && $avantEcheance;
    }
}
