<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ADesResponsables;

#[Fillable(['direction_id', 'code', 'nom', 'description'])]
class SousDirection extends Model
{
    use ADesResponsables; // responsable() et mandats : historique des responsables

    protected $table = 'sous_directions';

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
