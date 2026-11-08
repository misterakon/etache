<?php

namespace App\Support;

use App\Models\Affectation;
use App\Models\MandatResponsable;
use App\Models\ProfilAcces;
use App\Models\RoleAffectation;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TacheHistorique;
use App\Models\TacheJustification;
use App\Models\User;
use App\Notifications\JustificationNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Regles de la justification de retard (voir reprise §5.1).
 */
class JustificationRetard
{
    /** Report de ce nombre de jours (ou moins) ET premier report : decide par le responsable de direction. */
    const SEUIL_JOURS_RD = 15;

    const MOTIFS = [
        'dependance_externe' => 'Dépendance externe',
        'manque_ressources' => 'Manque de ressources',
        'blocage_administratif' => 'Blocage administratif',
        'arbitrage_en_attente' => 'Arbitrage en attente',
        'changement_perimetre' => 'Changement de périmètre',
        'autre' => 'Autre',
    ];

    public static function tacheOuverte(Tache $tache): bool
    {
        return ! in_array($tache->statutExecution?->code, [StatutExecution::TERMINEE, StatutExecution::ANNULEE], true);
    }

    /** Responsable ou pilote de la tache (pas les contributeurs), ou administrateur. */
    public static function peutDemander(User $user, Tache $tache): bool
    {
        if ($user->hasRole('ADMIN')) {
            return true;
        }
        if (! $user->personnel_id) {
            return false;
        }

        return $tache->affectations()
            ->where('personnel_id', $user->personnel_id)
            ->whereIn('role_affectation_id', RoleAffectation::whereIn('code', [RoleAffectation::RESPONSABLE, RoleAffectation::PILOTE])->select('id'))
            ->exists();
    }

    public static function enAttente(Tache $tache): ?TacheJustification
    {
        return TacheJustification::enAttente()->where('tache_id', $tache->id)->first();
    }

    /** Reports deja accordes : lignes date_fin de l'historique dont la nouvelle date est posterieure a l'ancienne. */
    public static function nbReports(Tache $tache): int
    {
        return TacheHistorique::where('tache_id', $tache->id)
            ->where('champ', 'date_fin')
            ->whereNotNull('ancienne_valeur')
            ->whereNotNull('nouvelle_valeur')
            ->whereColumn('nouvelle_valeur', '>', 'ancienne_valeur')
            ->count();
    }

    public static function peutDecider(User $user, TacheJustification $j): bool
    {
        return $j->statut === TacheJustification::EN_ATTENTE
            && TacheJustification::aDecider($user)->whereKey($j->id)->exists();
    }

    /**
     * Niveau et agent decideur d'une nouvelle demande.
     *  - justification seule : responsable de direction ;
     *  - report <= 15 j ET 1er report : responsable de direction ; sinon DG ;
     *  - si le demandeur EST le responsable de direction (ou n'en a pas), la demande monte au DG.
     *
     * @return array{0: string, 1: int|null} [niveau, personnel_id du decideur]
     */
    public static function decideurPour(User $demandeur, string $type, int $jours, int $nbReports): array
    {
        $agent = $demandeur->personnel;
        $niveau = ($type === TacheJustification::TYPE_JUSTIFICATION
            || ($jours <= self::SEUIL_JOURS_RD && $nbReports === 0))
            ? TacheJustification::NIVEAU_RD
            : TacheJustification::NIVEAU_DG;

        if ($niveau === TacheJustification::NIVEAU_RD && $agent) {
            $rd = $agent->service?->direction()?->responsable(today());
            if ($rd && $rd->id !== $agent->id) {
                return [TacheJustification::NIVEAU_RD, $rd->id];
            }
        }

        return [TacheJustification::NIVEAU_DG, MandatResponsable::directeurGeneral(today())?->id];
    }

    /** Nombre de demandes en attente que l'utilisateur peut decider (compteur du menu). */
    public static function nbAValider(User $user): int
    {
        if (! $user->hasRole('ADMIN|DG|RD')) {
            return 0;
        }

        return TacheJustification::enAttente()->aDecider($user)->count();
    }

    /**
     * Retards "non justifies" : en retard, sans demande en attente et sans justification
     * approuvee valable pour l'echeance actuelle (un report accorde change l'echeance : il faut rejustifier).
     */
    public static function retardsNonJustifies(): Builder
    {
        return Tache::retardNonJustifie();
    }

    /** Cloche : prévient le décideur d'une nouvelle demande (jamais le demandeur lui-même). */
    public static function notifierDemande(TacheJustification $j): void
    {
        try {
            $destinataires = $j->decideur_personnel_id
                ? User::where('actif', true)->where('personnel_id', $j->decideur_personnel_id)->where('id', '!=', $j->demandeur_id)->get()
                : collect();

            if ($destinataires->isEmpty()) { // pas de décideur identifié : les administrateurs
                $destinataires = User::where('actif', true)->where('id', '!=', $j->demandeur_id)
                    ->whereHas('profilAcces', fn ($p) => $p->where('slug', ProfilAcces::ADMINISTRATEUR))->get();
            }

            foreach ($destinataires as $user) {
                $user->notify(new JustificationNotification($j, JustificationNotification::DEMANDE));
            }
        } catch (\Throwable $e) {
            report($e); // la cloche ne doit jamais bloquer la demande
        }
    }

    /** Cloche : informe le demandeur de la décision (sauf s'il l'a prise lui-même). */
    public static function notifierDecision(TacheJustification $j): void
    {
        try {
            if ($j->demandeur && $j->demandeur->id !== $j->decide_par) {
                $j->demandeur->notify(new JustificationNotification($j, JustificationNotification::DECISION));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Contexte d'affichage pour un lot de taches (boutons et badges), en 3 requetes maximum.
     *
     * @return array{demandables: array, enAttente: Collection, justifiees: array}
     *                                                                             demandables : ids des taches pour lesquelles l'utilisateur peut faire une demande
     *                                                                             enAttente   : demande en attente, indexee par tache_id
     *                                                                             justifiees  : ids des taches en retard dont la justification approuvee vaut pour l'echeance actuelle
     */
    public static function contexte(User $user, $taches): array
    {
        $taches = collect($taches)->keyBy('id');
        $ids = $taches->keys()->all();

        if (! $ids) {
            return ['demandables' => [], 'enAttente' => collect(), 'justifiees' => []];
        }

        if ($user->hasRole('ADMIN')) {
            $demandables = $ids;
        } elseif ($user->personnel_id) {
            $demandables = Affectation::where('affectable_type', (new Tache)->getMorphClass())
                ->whereIn('affectable_id', $ids)
                ->where('personnel_id', $user->personnel_id)
                ->whereIn('role_affectation_id', RoleAffectation::whereIn('code', [RoleAffectation::RESPONSABLE, RoleAffectation::PILOTE])->select('id'))
                ->pluck('affectable_id')->unique()->values()->all();
        } else {
            $demandables = [];
        }

        $enAttente = TacheJustification::enAttente()->whereIn('tache_id', $ids)->get()->keyBy('tache_id');

        $justifiees = TacheJustification::where('statut', TacheJustification::APPROUVEE)
            ->where('type', TacheJustification::TYPE_JUSTIFICATION)
            ->whereIn('tache_id', $ids)->get()
            ->filter(function ($j) use ($taches) {
                $fin = $taches[$j->tache_id]->date_fin;

                return $fin && $j->echeance_actuelle && $fin->isSameDay($j->echeance_actuelle);
            })
            ->pluck('tache_id')->unique()->values()->all();

        return compact('demandables', 'enAttente', 'justifiees');
    }
}
