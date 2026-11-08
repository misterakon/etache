<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ProtegeParCloture;

#[Fillable([
    'tache_id', 'demandeur_id', 'type', 'motif', 'commentaire',
    'echeance_actuelle', 'echeance_proposee', 'statut', 'niveau_decision',
    'decideur_personnel_id', 'decide_par', 'decision_commentaire', 'decide_le',
])]
class TacheJustification extends Model
{
    use ProtegeParCloture;

    protected $table = 'tache_justifications';

    const TYPE_JUSTIFICATION = 'justification';
    const TYPE_REPORT = 'report';

    const EN_ATTENTE = 'en_attente';
    const APPROUVEE = 'approuvee';
    const REFUSEE = 'refusee';
    const ANNULEE = 'annulee';

    const NIVEAU_RD = 'RD';
    const NIVEAU_DG = 'DG';

    protected function casts(): array
    {
        return [
            'echeance_actuelle' => 'date',
            'echeance_proposee' => 'date',
            'decide_le' => 'datetime',
        ];
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function decideur()
    {
        return $this->belongsTo(User::class, 'decide_par');
    }

    public function decideurPersonnel()
    {
        return $this->belongsTo(Personnel::class, 'decideur_personnel_id');
    }

    public function estReport(): bool
    {
        return $this->type === self::TYPE_REPORT;
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', self::EN_ATTENTE);
    }

    public function exercicesProteges(): array
    {
        return [$this->tache?->exerciceTache()];
    }

    /**
     * Demandes qu'un utilisateur a le droit de VOIR (page des demandes et compteurs du tableau de bord) :
     *  - ADMIN : toutes ;
     *  - les autres : celles qu'il a faites, qu'il a décidées, ou qui lui sont adressées
     *    (le DG voit aussi toutes celles de niveau DG).
     */
    public function scopeVisiblePar(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN')) {
            return $query;
        }

        return $query->where(function (Builder $w) use ($user) {
            $w->where('demandeur_id', $user->id)->orWhere('decide_par', $user->id);

            if ($user->personnel_id) { // sans agent lié : ne jamais comparer à NULL (cela renverrait les demandes sans décideur)
                $w->orWhere('decideur_personnel_id', $user->personnel_id);
            }
            if ($user->hasRole('DG')) {
                $w->orWhere('niveau_decision', self::NIVEAU_DG);
            }
        });
    }

    /**
     * Demandes qu'un utilisateur a le droit de decider (source unique des droits de decision) :
     *  - ADMIN : toutes ;  DG : niveau DG ;  RD : niveau RD qui lui est adressees.
     * Personne ne decide sa propre demande.
     */
    public function scopeADecider(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN')) {
            return $query->where('demandeur_id', '!=', $user->id);
        }

        $estDg = $user->hasRole('DG');
        $estRd = $user->hasRole('RD') && $user->personnel_id;

        if (! $estDg && ! $estRd) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('demandeur_id', '!=', $user->id)
            ->where(function (Builder $w) use ($estDg, $estRd, $user) {
                if ($estDg) {
                    $w->orWhere('niveau_decision', self::NIVEAU_DG);
                }
                if ($estRd) {
                    $w->orWhere(fn ($x) => $x->where('niveau_decision', self::NIVEAU_RD)
                        ->where('decideur_personnel_id', $user->personnel_id));
                }
            });
    }
}
