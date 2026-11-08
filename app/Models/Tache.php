<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeAnnuel;
use App\Models\Concerns\ProtegeParCloture;

#[Fillable([
    'code', 'action_mesurable_id', 'libelle', 'indicateur', 'livrable', 'ordre',
    'date_debut', 'date_fin', 'duree_jours', 'priorite_id', 'budget_estime',
    'statut_execution_id', 'situation_temporelle_id', 'avancement_pourcentage',
    'observations', 'derniere_mise_a_jour_le', 'reconduite_de_id', 'reconduite_vers_exercice_id',
    'cloture_validation', 'cloture_soumise_le', 'cloture_soumise_par', 'cloture_validee_le', 'cloture_validee_par', 'cloture_motif_refus', 'cloture_relance_le',
])]
class Tache extends Model
{
    use GenereCodeAnnuel;
    use GenereCodeAnnuel, ProtegeParCloture;

    protected $table = 'taches';
    protected static string $prefixeCode = 'TA';
    protected static int $largeurCode = 3; // TA26-001, TA26-002...

    public function anneeCode(): string
    {
        $exercice = $this->actionMesurable?->actionCle?->axe?->exercice;

        return $exercice ? $exercice->anneeCourte() : now()->format('y');
    }

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'budget_estime' => 'decimal:2',
            'derniere_mise_a_jour_le' => 'datetime',
            'cloture_soumise_le' => 'datetime',
            'cloture_validee_le' => 'datetime',
            'cloture_relance_le' => 'datetime',
        ];
    }

    // --- Relations directes ---

    public function actionMesurable()
    {
        return $this->belongsTo(ActionMesurable::class);
    }

    public function priorite()
    {
        return $this->belongsTo(Priorite::class);
    }

    public function statutExecution()
    {
        return $this->belongsTo(StatutExecution::class);
    }

    public function situationTemporelle()
    {
        return $this->belongsTo(SituationTemporelle::class);
    }

    public function justifications()
    {
        return $this->hasMany(TacheJustification::class);
    }

    public function piecesJointes()
    {
        return $this->morphMany(PieceJointe::class, 'attachable')->where('actuelle', true)->orderByDesc('created_at');
    }

    // --- Affectations (responsable/pilote/contributeur) ---

    public function affectations()
    {
        return $this->morphMany(Affectation::class, 'affectable');
    }

    public function responsables()
    {
        return $this->affectations()->whereIn(
            'role_affectation_id',
            RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->select('id')
        );
    }

    public function pilotes()
    {
        return $this->affectations()->whereIn(
            'role_affectation_id',
            RoleAffectation::where('code', RoleAffectation::PILOTE)->select('id')
        );
    }

    public function sousTaches()
    {
        return $this->hasMany(SousTache::class)->orderBy('ordre')->orderBy('id');
    }

    /** Charge sous_taches_count et sous_taches_cochees_count (indicateur "3/5"). */
    public function scopeAvecCompteSousTaches(Builder $query): Builder
    {
        return $query->withCount([
            'sousTaches',
            'sousTaches as sous_taches_cochees_count' => fn ($q) => $q->where('cochee', true),
            'piecesJointes',
        ]);
    }

    public function exerciceTache(): ?Exercice
    {
        return $this->actionMesurable?->actionCle?->axe?->exercice;
    }

    /** Exercices concernés (actuel et d'origine) : une tâche d'un exercice clos ne peut être ni modifiée ni déplacée. */
    public function exercicesProteges(): array
    {
        $exerciceDe = fn ($amId) => $amId ? ActionMesurable::find($amId)?->actionCle?->axe?->exercice : null;

        return [
            $exerciceDe($this->action_mesurable_id),
            $this->exists && $this->isDirty('action_mesurable_id') ? $exerciceDe($this->getOriginal('action_mesurable_id')) : null,
        ];
    }

    public function reconduiteDe()
    {
        return $this->belongsTo(self::class, 'reconduite_de_id');
    }

    public function reconduiteVersExercice()
    {
        return $this->belongsTo(Exercice::class, 'reconduite_vers_exercice_id');
    }

    /** Exclut les tâches des exercices clos (alertes et situations ne les concernent plus). */
    public function scopeExerciceOuvert(Builder $query): Builder
    {
        return $query->whereDoesntHave('actionMesurable.actionCle.axe.exercice', fn ($e) => $e->whereNotNull('cloture_le'));
    }

    // --- Dépendances entre tâches (auto-référence via tache_dependance) ---

    /**
     * Les tâches dont CETTE tâche dépend (doivent être faites avant).
     */
    public function dependances()
    {
        return $this->belongsToMany(
            Tache::class,
            'tache_dependance',
            'tache_id',
            'depend_de_tache_id'
        )->withTimestamps();
    }

    /**
     * Les tâches qui dépendent de celle-ci (bloquées si celle-ci prend du retard).
     */
    public function dependantes()
    {
        return $this->belongsToMany(
            Tache::class,
            'tache_dependance',
            'depend_de_tache_id',
            'tache_id'
        )->withTimestamps();
    }

    // --- Historique et alertes ---

    public function historiques()
    {
        return $this->hasMany(TacheHistorique::class)->latest('created_at');
    }

    public function alertesEnvoyees()
    {
        return $this->hasMany(AlerteEnvoyee::class);
    }

    // --- Accès à la direction, via les affectations (pas de colonne dénormalisée) ---

    /**
     * Directions distinctes des personnes affectées à cette tâche.
     * Retourne une Collection, potentiellement vide si personne n'est encore affecté.
     */
    public function directions()
    {
        return $this->affectations()
            ->whereIn('role_affectation_id', RoleAffectation::whereIn('code', [RoleAffectation::RESPONSABLE, RoleAffectation::PILOTE])->select('id'))
            ->with('personnel.service.sousDirection.direction', 'personnel.service.directionDirecte')
            ->get()
            ->map(fn ($a) => $a->personnel?->service?->direction())
            ->filter()
            ->unique('id')
            ->values();
    }

    /** Directions porteuses : celles de l'action clé (principale + associées). */
    public function directionsPorteuses()
    {
        return $this->actionMesurable?->actionCle?->directionsPorteuses() ?? collect();
    }

    /** Clôture soumise, en attente de la validation du supérieur hiérarchique. */
    public function enAttenteValidation(): bool
    {
        return $this->cloture_validation === 'a_valider';
    }

    // --- Etat calcule "en retard" (independant du moteur d'alertes) ---

    /**
     * En retard = echeance depassee ET tache ni terminee ni annulee.
     * Necessite la relation statutExecution chargee (eager loading) pour eviter une requete par ligne.
     */
    public function estEnRetard(): bool
    {
        if (! $this->date_fin || ! $this->date_fin->isBefore(today())) {
            return false;
        }
        // Clôture soumise à temps et en attente de validation : le retard du valideur n'est pas imputé au responsable
        if ($this->cloture_validation === 'a_valider' && $this->cloture_soumise_le && $this->cloture_soumise_le->lte($this->date_fin->copy()->endOfDay())) {
            return false;
        }

        return ! in_array(
            $this->statutExecution?->code,
            [StatutExecution::TERMINEE, StatutExecution::ANNULEE],
            true
        );
    }

    /**
     * '' (pas en retard) | 'attente' | 'justifie' | 'non_justifie'.
     * Même règle que le scope retardNonJustifie. Nécessite la relation justifications chargée.
     */
    public function etatJustification(): string
    {
        if (! $this->estEnRetard()) {
            return '';
        }
        if ($this->justifications->contains('statut', TacheJustification::EN_ATTENTE)) {
            return 'attente';
        }
        $valable = $this->justifications->contains(fn ($j) => $j->statut === TacheJustification::APPROUVEE
            && $j->type === TacheJustification::TYPE_JUSTIFICATION
            && $j->echeance_actuelle?->isSameDay($this->date_fin));

        return $valable ? 'justifie' : 'non_justifie';
    }

    // --- Scopes ---

    /**
     * Version calculee a la volee (date du jour), utilisable des maintenant.
     */
    public function scopeEnRetardCalcule(Builder $query): Builder
    {
        $idsClos = StatutExecution::whereIn('code', [StatutExecution::TERMINEE, StatutExecution::ANNULEE])->pluck('id');

        return $query->whereNotNull('date_fin')
            ->whereDate('date_fin', '<', today())
            ->whereNotIn('statut_execution_id', $idsClos)
            // hors clôture soumise à temps (en attente de validation)
            ->where(fn ($q) => $q->whereNull('cloture_validation')->orWhere('cloture_validation', '!=', 'a_valider')
                ->orWhereNull('cloture_soumise_le')->orWhereRaw('DATE(cloture_soumise_le) > date_fin'));
    }

    /**
     * Basee sur la colonne situation_temporelle, alimentee par le futur moteur d'alertes.
     */
    public function scopeEnRetard(Builder $query): Builder
    {
        return $query->whereIn(
            'situation_temporelle_id',
            SituationTemporelle::where('code', SituationTemporelle::EN_RETARD)->select('id')
        );
    }

    public function scopeARisque(Builder $query): Builder
    {
        return $query->whereIn(
            'situation_temporelle_id',
            SituationTemporelle::where('code', SituationTemporelle::A_RISQUE)->select('id')
        );
    }

    public function scopeNonTerminees(Builder $query): Builder
    {
        return $query->whereNotIn(
            'statut_execution_id',
            StatutExecution::whereIn('code', [StatutExecution::TERMINEE, StatutExecution::ANNULEE])->select('id')
        );
    }

    public function scopeParAxe(Builder $query, int $axeId): Builder
    {
        return $query->whereHas(
            'actionMesurable.actionCle',
            fn ($q) => $q->where('axe_id', $axeId)
        );
    }

    /**
     * Filtre direction. $mode : 'porteuse' (principale ou associée de l'action clé), 'impliquee'
     * (responsable ou pilote d'une direction), 'toutes' (l'un ou l'autre). Les contributeurs n'élargissent pas le périmètre.
     */
    public function scopeParDirection(Builder $query, int|array $directions, string $mode = 'toutes'): Builder
    {
        $ids = array_map('intval', (array) $directions);
        $roles = fn () => RoleAffectation::whereIn('code', [RoleAffectation::RESPONSABLE, RoleAffectation::PILOTE])->select('id');

        $porteuse = fn (Builder $q) => $q->whereHas('actionMesurable.actionCle', fn ($ac) => $ac->where(
            fn ($x) => $x->whereIn('direction_id', $ids)
                ->orWhereHas('directionsAssociees', fn ($d) => $d->whereIn('directions.id', $ids))
        ));

        $impliquee = fn (Builder $q) => $q->whereHas('affectations', fn ($a) => $a
            ->whereIn('role_affectation_id', $roles())
            ->whereHas('personnel.service', fn ($s) => $s->whereIn('direction_id', $ids)
                ->orWhereHas('sousDirection', fn ($sd) => $sd->whereIn('direction_id', $ids))));

        return match ($mode) {
            'porteuse' => $query->where($porteuse),
            'impliquee' => $query->where($impliquee),
            default => $query->where(fn ($w) => $w->where($porteuse)->orWhere($impliquee)),
        };
    }

    public function scopeDansExercice(Builder $query, ?int $exerciceId): Builder
    {
        return $query->when($exerciceId, fn ($q) => $q->whereHas(
            'actionMesurable.actionCle.axe', fn ($a) => $a->where('exercice_id', $exerciceId)
        ));
    }

    /** Même règle que l'écran : ADMIN, DG et RD voient tout ; les autres, leurs tâches. */
    public function scopeVisiblePar(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN|DG|RD')) {
            return $query;
        }

        return $user->personnel_id
            ? $query->filtrer(['personnel_id' => $user->personnel_id])
            : $query->whereRaw('1 = 0');
    }

    
    /** En retard, sans demande en attente ni justification approuvée valable pour l'échéance actuelle. */
    public function scopeRetardNonJustifie(Builder $query): Builder
    {
        return $query->enRetardCalcule()->whereDoesntHave('justifications', self::justificationValable());
    }

    public function scopeRetardJustifie(Builder $query): Builder
    {
        return $query->enRetardCalcule()->whereHas('justifications', self::justificationValable());
    }

    private static function justificationValable(): \Closure
    {
        return fn ($q) => $q->where(function ($w) {
            $w->where('statut', TacheJustification::EN_ATTENTE)
                ->orWhere(fn ($x) => $x->where('statut', TacheJustification::APPROUVEE)
                    ->where('type', TacheJustification::TYPE_JUSTIFICATION)
                    ->whereColumn('tache_justifications.echeance_actuelle', 'taches.date_fin'));
        });
    }

    /**
     * Point d'entree unique des filtres de la page Taches : toutes les vues
     * (liste, tableau, kanban, arborescence) et les futurs exports passent par ici.
     */
    public function scopeFiltrer(Builder $query, array $f): Builder
    {
        if (! empty($f['q'])) {
            $motif = '%'.$f['q'].'%';
            $query->where(function ($w) use ($motif) {
                $w->where('code', 'like', $motif)
                    ->orWhere('libelle', 'like', $motif)
                    ->orWhere('indicateur', 'like', $motif)
                    ->orWhere('livrable', 'like', $motif)
                    ->orWhere('observations', 'like', $motif);
            });
        }

        if (! empty($f['axe_id'])) {
            $query->parAxe((int) $f['axe_id']);
        }
        if (! empty($f['action_cle_id'])) {
            $query->whereHas('actionMesurable', fn ($q) => $q->where('action_cle_id', $f['action_cle_id']));
        }
        if (! empty($f['action_mesurable_id'])) {
            $query->where('action_mesurable_id', $f['action_mesurable_id']);
        }
        if (! empty($f['statut_execution_id'])) {
            $query->where('statut_execution_id', $f['statut_execution_id']);
        }
        if (! empty($f['priorite_id'])) {
            $query->where('priorite_id', $f['priorite_id']);
        }
        if (! empty($f['date_fin_du'])) {
            $query->whereDate('date_fin', '>=', $f['date_fin_du']);
        }
        if (! empty($f['date_fin_au'])) {
            $query->whereDate('date_fin', '<=', $f['date_fin_au']);
        }
        if (! empty($f['en_retard'])) {
            $query->enRetardCalcule();
        }
        if (! empty($f['direction_id'])) {
            $query->parDirection((int) $f['direction_id'], $f['direction_role'] ?? 'toutes');
        }
        if (! empty($f['non_justifie'])) {
            $query->retardNonJustifie();
        }

        $rolesFiltres = [
            'responsable_id' => RoleAffectation::RESPONSABLE,
            'pilote_id' => RoleAffectation::PILOTE,
            'contributeur_id' => RoleAffectation::CONTRIBUTEUR,
        ];
        foreach ($rolesFiltres as $cle => $codeRole) {
            if (! empty($f[$cle])) {
                $query->whereHas('affectations', function ($a) use ($f, $cle, $codeRole) {
                    $a->where('personnel_id', $f[$cle])
                        ->whereIn('role_affectation_id', RoleAffectation::where('code', $codeRole)->select('id'));
                });
            }
        }

        // "Mes taches" : affectee a l'agent lie au compte connecte, quel que soit son role
        if (! empty($f['personnel_id'])) {
            $query->whereHas('affectations', fn ($a) => $a->where('personnel_id', $f['personnel_id']));
        }

        return $query;
    }
}
