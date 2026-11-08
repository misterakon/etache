<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'libelle', 'delai_jours', 'saisie_libre', 'ordre', 'actif'])]
class DelaiCourrier extends Model
{
    protected $table = 'delais_courrier';

    protected function casts(): array
    {
        return ['actif' => 'boolean', 'saisie_libre' => 'boolean', 'delai_jours' => 'integer'];
    }
}
