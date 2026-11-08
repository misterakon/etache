<?php

namespace App\Support;

use App\Models\Courrier;
use App\Models\ImputationDestinataire;
use App\Models\ReponseVisa;
use App\Models\User;

/**
 * Droits sur les courriers.
 *  - consulter le registre / enregistrer : profils des Réglages (Courrier) ;
 *  - voir un courrier confidentiel : ADMIN, DG, la personne qui l'a enregistré, et les personnes désignées par une imputation ;
 *  - modifier : profils d'enregistrement, tant que le courrier n'est pas imputé.
 */
class CourrierDroits
{
    public static function peutConsulterRegistre(?User $user): bool
    {
        return $user !== null && $user->hasRole(Reglages::get('courrier.roles_registre'));
    }

    public static function peutEnregistrer(?User $user): bool
    {
        return $user !== null && $user->hasRole(Reglages::get('courrier.roles_enregistrement'));
    }

    /** Imputer un courrier : profils des Réglages (DG et ADMIN par défaut). */
    public static function peutImputer(?User $user): bool
    {
        return $user !== null && $user->hasRole(Reglages::get('courrier.roles_imputation'));
    }

    /** Personne désignée par une imputation (responsable d'une diligence du courrier). */
    public static function aUneDiligence(Courrier $courrier, ?User $user): bool
    {
        if ($user?->personnel_id === null) {
            return false;
        }
        if ($courrier->diligences()->where('responsable_personnel_id', $user->personnel_id)->exists()) {
            return true;
        }
        // personne désignée dans le circuit de visa d'une réponse à ce courrier : elle doit pouvoir lire ce à quoi elle répond
        if (ReponseVisa::where('personnel_id', $user->personnel_id)
            ->whereHas('reponse', fn ($r) => $r->where('courrier_id', $courrier->id)->where('statut', '!=', 'annulee'))->exists()) {
            return true;
        }

        $clesCibles = DestinatairesImputation::clesAssocieesAuPersonnel((int) $user->personnel_id);
        if ($clesCibles === []) {
            return false;
        }

        return ImputationDestinataire::whereIn('role', ['avis', 'copie'])
            ->whereHas('imputation', fn ($query) => $query->where('courrier_id', $courrier->id))
            ->get()
            ->contains(fn ($destinataire) => in_array($destinataire->destinataire_type.':'.$destinataire->destinataire_id, $clesCibles, true));
    }

    public static function peutVoir(Courrier $courrier, ?User $user): bool
    {
        if (self::aUneDiligence($courrier, $user)) {
            return true; // les personnes désignées voient le courrier, même confidentiel
        }
        if (! self::peutConsulterRegistre($user)) {
            return false;
        }
        if (! $courrier->estConfidentiel()) {
            return true;
        }

        return $user->hasRole('ADMIN|DG') || $courrier->enregistre_par === $user->id;
    }

    public static function peutModifier(Courrier $courrier, ?User $user): bool
    {
        return self::peutEnregistrer($user) && self::peutVoir($courrier, $user) && $courrier->statut === 'enregistre';
    }
}
