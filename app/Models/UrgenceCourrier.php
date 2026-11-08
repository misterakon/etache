<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'libelle', 'delai_jours', 'couleur', 'ordre', 'actif'])]
class UrgenceCourrier extends Model
{
    protected $table = 'urgences_courrier';

    protected function casts(): array
    {
        return ['actif' => 'boolean', 'delai_jours' => 'integer'];
    }

    public function courriers()
    {
        return $this->hasMany(Courrier::class, 'urgence_id');
    }
}
