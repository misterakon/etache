<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['libelle', 'ordre', 'actif'])]
class InstructionCourrier extends Model
{
    protected $table = 'instructions_courrier';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }
}
