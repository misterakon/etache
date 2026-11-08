<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Une étape du circuit de visa d'une réponse (la dernière est la signature du Directeur Général). */
#[Fillable(['reponse_id', 'cycle', 'ordre', 'personnel_id', 'libelle', 'signataire', 'statut', 'decide_par', 'decide_le', 'commentaire'])]
class ReponseVisa extends Model
{
    protected $table = 'reponse_visas';

    const STATUTS = [
        'en_attente' => ['En attente', '#C77D0A'],
        'vise' => ['Visé', '#2E7D5B'],
        'refuse' => ['Refusé', '#A73B34'],
        'annule' => ['Annulé', '#8A94A6'],
    ];

    protected function casts(): array
    {
        return ['signataire' => 'boolean', 'decide_le' => 'datetime'];
    }

    public function reponse()
    {
        return $this->belongsTo(ReponseCourrier::class, 'reponse_id');
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
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
