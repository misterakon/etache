<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Demande de délai (report d'échéance) sur une diligence, décidée par celui qui a imputé ou le niveau supérieur. */
#[Fillable([
    'diligence_id', 'demande_par', 'echeance_actuelle', 'echeance_souhaitee', 'motif', 'statut',
    'decide_par', 'decide_le', 'echeance_accordee', 'motif_decision',
])]
class DiligenceDelai extends Model
{
    protected $table = 'diligence_delais';

    const STATUTS = [
        'en_attente' => ['En attente', '#C77D0A'],
        'accorde' => ['Accordé', '#2E7D5B'],
        'refuse' => ['Refusé', '#A73B34'],
    ];

    protected function casts(): array
    {
        return ['echeance_actuelle' => 'date', 'echeance_souhaitee' => 'date', 'echeance_accordee' => 'date', 'decide_le' => 'datetime'];
    }

    public function diligence()
    {
        return $this->belongsTo(Diligence::class);
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demande_par');
    }

    public function decideur()
    {
        return $this->belongsTo(User::class, 'decide_par');
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut][0] ?? $this->statut;
    }

    public function couleurStatut(): string
    {
        return self::STATUTS[$this->statut][1] ?? '#5B6B84';
    }
}
