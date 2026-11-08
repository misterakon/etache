<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'ordre'])]
class Grade extends Model
{
    protected $table = 'grades';
}
