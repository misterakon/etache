<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\DiligenceAction;
use App\Models\ImputationDestinataire;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\PosteImputation;
use App\Models\ReponseVisa;
use App\Models\User;

/**
 * Droits sur une diligence.
 *  - voir : toute personne qui peut voir le courrier, et les supérieurs hiérarchiques du responsable ;
 *  - agir (prendre en charge, solder) : le responsable de la diligence, ADMIN et DG ;
 *  - annuler : les profils qui imputent (réglage), avec un motif.
 */
class DiligenceDroits
{
    public static function estResponsable(Diligence $d, ?User $user): bool
    {
        return $user !== null && $user->personnel_id !== null && (int) $d->responsable_personnel_id === (int) $user->personnel_id;
    }

    public static function estSuperieur(Diligence $d, ?User $user): bool
    {
        if (! $user?->personnel_id || ! $d->responsable) {
            return false;
        }

        $ids = collect(DestinatairesImputation::superieursDe($d->responsable))->pluck('id')
            ->merge(collect(ChaineHierarchique::auDessus($d->responsable))->map(fn ($e) => $e['personnel']->id));

        return $ids->contains((int) $user->personnel_id);
    }

    public static function voir(Diligence $d, ?User $user): bool
    {
        return $user !== null && (CourrierDroits::peutVoir($d->courrier, $user) || self::estResponsable($d, $user) || self::estSuperieur($d, $user) || self::estVisaDe($d, $user));
    }

    /** Personne désignée dans le circuit de visa d'une réponse de cette diligence. */
    public static function estVisaDe(Diligence $d, ?User $user): bool
    {
        return $user?->personnel_id !== null
            && ReponseVisa::where('personnel_id', $user->personnel_id)->whereHas('reponse', fn ($q) => $q->where('diligence_id', $d->id))->exists();
    }

    /** Plan de traitement, réponses : le responsable de la diligence, ADMIN et DG, tant qu'elle est ouverte. */
    public static function gererActions(Diligence $d, ?User $user): bool
    {
        return $user !== null && $d->estOuverte() && (self::estResponsable($d, $user) || $user->hasRole('ADMIN|DG'));
    }

    /** Validateur des actions : supérieur direct du responsable dans la chaîne chef de service, sous-directeur, directeur, DG (le DG à défaut). */
    public static function validateur(Diligence $d): ?Personnel
    {
        if (! $d->responsable) {
            return MandatResponsable::directeurGeneral();
        }

        return ChaineHierarchique::auDessus($d->responsable)[0]['personnel'] ?? MandatResponsable::directeurGeneral();
    }

    /** Renvoyer le dossier : au supérieur (diligence réimputée) ou à celui qui a imputé (diligence reçue directement). Réservé au responsable. */
    public static function renvoyer(Diligence $d, ?User $user): bool
    {
        $d->loadMissing('diligenceParente', 'imputation');

        return $user !== null && $d->estOuverte() && self::estResponsable($d, $user) && ($d->diligenceParente !== null || $d->imputation !== null);
    }

    public static function peutValiderAction(Diligence $d, ?User $user): bool
    {
        return $user !== null && $d->estOuverte() && ($user->hasRole('ADMIN|DG')
            || ($user->personnel_id && (int) self::validateur($d)?->id === (int) $user->personnel_id));
    }

    /** Cocher une action : validée, par le responsable de la diligence (ADMIN et DG compris). */
    public static function peutCocherAction(DiligenceAction $a, ?User $user): bool
    {
        return $user !== null && $a->estValidee() && $a->diligence->estOuverte() && self::gererActions($a->diligence, $user);
    }

    public static function peutDemanderDelai(Diligence $d, ?User $user): bool
    {
        return self::agir($d, $user) && ! $d->delais()->where('statut', 'en_attente')->exists();
    }

    /** Décider d'un report : celui qui a imputé, le responsable de la diligence parente, le DG et l'ADMIN. */
    public static function peutDeciderDelai(Diligence $d, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }
        if ($user->hasRole('ADMIN|DG')) {
            return true;
        }
        $d->loadMissing('imputation', 'diligenceParente');
        if ($d->imputation && (int) $d->imputation->imputee_par === (int) $user->id) {
            return true;
        }

        return $d->diligenceParente !== null && self::estResponsable($d->diligenceParente, $user);
    }

    public static function agir(Diligence $d, ?User $user): bool
    {
        return $user !== null && $d->estOuverte() && (self::estResponsable($d, $user) || $user->hasRole('ADMIN|DG'));
    }

    public static function reimputer(Diligence $d, ?User $user): bool
    {
        if ($user === null || ! $d->estOuverte()) {
            return false;
        }
        // Réglage « réimputation d'un courrier confidentiel réservée au DG » : la confidentialité ne s'étend plus sans lui
        if ($d->courrier?->estConfidentiel() && Reglages::get('courrier.reimputation_confidentiel_dg_seul') && ! $user->hasRole('ADMIN|DG')) {
            return false;
        }
        if ($user->hasRole('ADMIN')) {
            return true;
        }
        if (! self::estResponsable($d, $user)) {
            return false;
        }

        return self::mandatsReimputation($user)->isNotEmpty();
    }

    public static function peutDeposerAvis(Diligence $d, ?User $user): bool
    {
        if ($user?->personnel_id === null || ! $d->estOuverte()) {
            return false;
        }

        return $d->avisAssocies()->get()->contains(fn ($destinataire) => self::estDestinataireAvis($destinataire, $user));
    }

    public static function estDestinataireAvis(ImputationDestinataire $destinataire, ?User $user): bool
    {
        if ($user?->personnel_id === null || $destinataire->role !== 'avis') {
            return false;
        }
        $cible = DestinatairesImputation::resoudre($destinataire->destinataire_type, $destinataire->destinataire_id);

        return (int) ($cible['responsable']?->id ?? 0) === (int) $user->personnel_id;
    }

    public static function annuler(Diligence $d, ?User $user): bool
    {
        return $user !== null && $d->estOuverte() && CourrierDroits::peutImputer($user);
    }

    /** Mandats en cours donnant le droit de réimputer : tous, sauf poste configuré « ne réimpute pas ». */
    public static function mandatsReimputation(User $user)
    {
        return MandatResponsable::where('personnel_id', $user->personnel_id)->actifA()->get()->reject(function ($m) {
            $cle = MandatResponsable::cleUnite($m->niveau);

            return PosteImputation::where('actif', true)->where('niveau', $m->niveau)->where('unite_id', $cle ? $m->{$cle} : null)
                ->where('peut_reimputer', false)->exists();
        });
    }
}
