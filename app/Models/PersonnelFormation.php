<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['personnel_id', 'intitule', 'organisme', 'date_debut', 'date_fin', 'duree_heures', 'certifiante', 'observations', 'cree_par'])]
class PersonnelFormation extends Model
{
    protected $table = 'personnel_formations';

    protected function casts(): array
    {
        return ['date_debut' => 'date', 'date_fin' => 'date', 'certifiante' => 'boolean'];
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
