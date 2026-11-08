<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'libelle', 'ordre', 'actif'])]
class TypeCourrier extends Model
{
    protected $table = 'types_courrier';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function courriers()
    {
        return $this->hasMany(Courrier::class, 'type_courrier_id');
    }
}
