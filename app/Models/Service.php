<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ADesResponsables;

#[Fillable(['sous_direction_id', 'direction_id', 'code', 'nom', 'description'])]
class Service extends Model
{
    use ADesResponsables; // responsable() et mandats : historique des responsables

    protected $table = 'services';

    public function sousDirection()
    {
        return $this->belongsTo(SousDirection::class);
    }

    /**
     * Rattachement direct a une direction (sans sous-direction),
     * ex: "Service Financier et des Moyens Generaux".
     */
    public function directionDirecte()
    {
        return $this->belongsTo(Direction::class, 'direction_id');
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }

    /**
     * La direction reelle du service, qu'il soit rattache via une sous-direction
     * ou directement (jamais les deux en meme temps).
     */
    public function direction(): ?Direction
    {
        return $this->directionDirecte ?? $this->sousDirection?->direction;
    }
}
