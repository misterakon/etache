<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Valeur modifiée d'un réglage. Ne s'utilise pas directement : passer par App\Support\Reglages. */
#[Fillable(['cle', 'valeur', 'modifie_par'])]
class Reglage extends Model
{
    protected $table = 'reglages';
}
