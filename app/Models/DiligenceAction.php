<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Action du plan de traitement d'une diligence : un agent, des modalités, validée par le supérieur hiérarchique. */
#[Fillable([
    'diligence_id', 'libelle', 'description', 'personnel_id', 'echeance', 'modalites', 'conditions', 'ordre',
    'faite', 'faite_le', 'faite_par', 'statut_validation', 'valide_par', 'valide_le', 'motif_refus', 'cree_par',
])]
class DiligenceAction extends Model
{
    protected $table = 'diligence_actions';

    const VALIDATION = [
        'a_valider' => ['À valider', '#C77D0A'],
        'validee' => ['Validée', '#2E7D5B'],
        'refusee' => ['Refusée', '#A73B34'],
    ];

    protected function casts(): array
    {
        return ['echeance' => 'date', 'faite' => 'boolean', 'faite_le' => 'datetime', 'valide_le' => 'datetime'];
    }

    public function diligence()
    {
        return $this->belongsTo(Diligence::class);
    }

    public function agent()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function estValidee(): bool
    {
        return $this->statut_validation === 'validee';
    }

    public function scopeValidees($query)
    {
        return $query->where('statut_validation', 'validee');
    }

    public function libelleValidation(): string
    {
        return self::VALIDATION[$this->statut_validation][0] ?? $this->statut_validation;
    }

    public function couleurValidation(): string
    {
        return self::VALIDATION[$this->statut_validation][1] ?? '#5B6B84';
    }
}
