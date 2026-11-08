<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ADesResponsables;

#[Fillable(['code', 'nom', 'description'])]
class Direction extends Model
{
    use ADesResponsables; // responsable() et mandats : historique des responsables

    protected $table = 'directions';

    public function sousDirections()
    {
        return $this->hasMany(SousDirection::class);
    }

    public function actionsCles()
    {
        return $this->hasMany(ActionCle::class);
    }
}
