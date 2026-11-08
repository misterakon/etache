<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['personnel_id', 'libelle', 'niveau', 'commentaire', 'cree_par'])]
class PersonnelCompetence extends Model
{
    protected $table = 'personnel_competences';

    const NIVEAUX = [1 => 'Débutant', 2 => 'Intermédiaire', 3 => 'Avancé', 4 => 'Expert'];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function libelleNiveau(): string
    {
        return self::NIVEAUX[$this->niveau] ?? '—';
    }
}
