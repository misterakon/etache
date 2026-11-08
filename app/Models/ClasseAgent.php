<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['libelle', 'ordre'])]
class ClasseAgent extends Model
{
    protected $table = 'classes_agent';
}
