<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['numero'])]
class Echelon extends Model
{
    protected $table = 'echelons';
}
