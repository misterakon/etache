<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['famille_emploi_id', 'libelle', 'actif'])]
class Emploi extends Model
{
    protected $table = 'emplois';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function familleEmploi()
    {
        return $this->belongsTo(FamilleEmploi::class);
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }
}
