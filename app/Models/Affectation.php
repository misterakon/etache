<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['personnel_id', 'role_affectation_id', 'affectable_type', 'affectable_id'])]
class Affectation extends Model
{
    protected $table = 'affectations';

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function roleAffectation()
    {
        return $this->belongsTo(RoleAffectation::class);
    }

    /**
     * L'objet affecté : ActionCle, ActionMesurable ou Tache.
     */
    public function affectable()
    {
        return $this->morphTo();
    }
}
