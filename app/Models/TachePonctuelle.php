<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Tâche ponctuelle : travail d'un agent hors feuille de route et hors imputation de courrier.
 * Elle n'entre jamais dans l'avancement ni le budget de la feuille de route.
 */
class TachePonctuelle extends Model
{
    protected $table = 'taches_ponctuelles';

    protected $guarded = ['id'];

    public const A_FAIRE = 'a_faire';

    public const EN_COURS = 'en_cours';

    public const TERMINEE = 'terminee';

    public const ANNULEE = 'annulee';

    /** statut => [libellé, couleur, icône] */
    public const STATUTS = [
        self::A_FAIRE => ['À faire', '#C77D0A', 'fa-hourglass-half'],
        self::EN_COURS => ['En cours', '#2B6CB0', 'fa-bars-progress'],
        self::TERMINEE => ['Terminée', '#1F8A5B', 'fa-circle-check'],
        self::ANNULEE => ['Annulée', '#64748B', 'fa-ban'],
    ];

    public const VALIDATION_ATTENTE = 'en_attente';

    public const VALIDATION_REFUSEE = 'refusee';

    protected function casts(): array
    {
        return [
            'date_debut' => 'date', 'date_fin' => 'date', 'superieurs' => 'array',
            'decide_le' => 'datetime', 'terminee_le' => 'datetime', 'annulee_le' => 'datetime',
        ];
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'responsable_personnel_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par_user_id');
    }

    public function priorite(): BelongsTo
    {
        return $this->belongsTo(Priorite::class);
    }

    public function piecesJointes(): MorphMany
    {
        return $this->morphMany(PieceJointe::class, 'attachable')->where('actuelle', true)->orderByDesc('id');
    }

    /** Une tâche en attente de validation ou refusée ne compte ni dans les retards ni dans la charge. */
    public function estActive(): bool
    {
        return ! in_array($this->validation, [self::VALIDATION_ATTENTE, self::VALIDATION_REFUSEE], true) && $this->statut !== self::ANNULEE;
    }

    public function estOuverte(): bool
    {
        return $this->estActive() && in_array($this->statut, [self::A_FAIRE, self::EN_COURS], true);
    }

    public function estEnRetard(): bool
    {
        return $this->estOuverte() && $this->date_fin !== null && $this->date_fin->copy()->endOfDay()->isPast();
    }

    /** Tâches comptées : actives (ni en attente de validation, ni refusées, ni annulées). */
    public function scopeActives(Builder $q): Builder
    {
        return $q->whereNotIn('validation', [self::VALIDATION_ATTENTE, self::VALIDATION_REFUSEE])->where('statut', '!=', self::ANNULEE);
    }

    public function scopeOuvertes(Builder $q): Builder
    {
        return $q->actives()->whereIn('statut', [self::A_FAIRE, self::EN_COURS]);
    }

    public function scopeEnRetard(Builder $q): Builder
    {
        return $q->ouvertes()->whereNotNull('date_fin')->whereDate('date_fin', '<', today());
    }

    /** Ce que l'utilisateur a le droit de voir : ses tâches, celles qu'il a créées, celles de sa chaîne hiérarchique, tout pour ADMIN et DG. */
    public function scopeVisiblePar(Builder $q, User $user): Builder
    {
        if ($user->hasRole('ADMIN|DG')) {
            return $q;
        }

        return $q->where(function (Builder $w) use ($user) {
            $w->where('cree_par_user_id', $user->id);
            if ($user->personnel_id) {
                $w->orWhere('responsable_personnel_id', $user->personnel_id)->orWhereJsonContains('superieurs', (int) $user->personnel_id);
            }
        });
    }

    public static function prochainCode(): string
    {
        $annee = now()->format('y');
        $dernier = static::query()->where('code', 'like', 'TP'.$annee.'-%')->orderByDesc('id')->value('code');
        $n = $dernier ? ((int) substr($dernier, -4)) + 1 : 1;

        return sprintf('TP%s-%04d', $annee, $n);
    }
}
