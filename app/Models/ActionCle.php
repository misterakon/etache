<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ProtegeParCloture;

#[Fillable(['axe_id', 'direction_id', 'code', 'ordre', 'libelle', 'description'])]
class ActionCle extends Model
{
    use ProtegeParCloture;

    protected $table = 'actions_cles';

    public function axe()
    {
        return $this->belongsTo(Axe::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function actionsMesurables()
    {
        return $this->hasMany(ActionMesurable::class)->orderBy('ordre');
    }

    /**
     * Affectations (responsable/pilote/contributeur) directement sur cette action clé.
     */
    public function affectations()
    {
        return $this->morphMany(Affectation::class, 'affectable');
    }

    public function directionsAssociees()
    {
        return $this->belongsToMany(Direction::class, 'action_cle_direction')->withTimestamps();
    }

    /** Directions porteuses : principale d'abord, puis associées. */
    public function directionsPorteuses()
    {
        return collect([$this->direction])->merge($this->directionsAssociees)->filter()->unique('id')->values();
    }

    public function exercicesProteges(): array
    {
        return [$this->axe?->exercice];
    }
}
