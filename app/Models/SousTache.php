<?php

namespace App\Models;

use App\Models\Concerns\ProtegeParCloture;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'tache_id', 'libelle', 'description', 'echeance', 'ordre',
    'cochee', 'cochee_le', 'cochee_par', 'cree_par', 'poids',
    'personnel_id', 'modalites', 'conditions', 'statut_validation', 'valide_par', 'valide_le', 'motif_refus',
])]
class SousTache extends Model
{
    use ProtegeParCloture;

    protected $table = 'sous_taches';

    protected function casts(): array
    {
        return [
            'echeance' => 'date',
            'cochee' => 'boolean',
            'cochee_le' => 'datetime',
            'valide_le' => 'datetime',
        ];
    }

    public function exercicesProteges(): array
    {
        return [$this->tache?->exerciceTache()];
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function cocheePar()
    {
        return $this->belongsTo(User::class, 'cochee_par');
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /** Agent au bénéfice duquel la sous-tâche est confiée. */
    public function agent()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    /** Fichiers actuellement rattachés à cette sous-tâche. */
    public function piecesJointes(): MorphMany
    {
        return $this->morphMany(PieceJointe::class, 'attachable')
            ->where('actuelle', true)
            ->orderByDesc('created_at');
    }

    public function estValidee(): bool
    {
        return $this->statut_validation === 'validee';
    }

    /** Seules les sous-tâches validées comptent dans l'avancement. */
    public function scopeValidees($query)
    {
        return $query->where('statut_validation', 'validee');
    }
}
