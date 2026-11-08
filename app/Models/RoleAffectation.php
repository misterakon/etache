<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'actif'])]
class RoleAffectation extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'roles_affectation';

    // Codes seedés par RoleAffectationSeeder
    const RESPONSABLE = '001';
    const PILOTE = '002';
    const CONTRIBUTEUR = '003';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'role_affectation_id');
    }
}
