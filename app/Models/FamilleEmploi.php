<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'actif'])]
class FamilleEmploi extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'familles_emploi';

    // Codes seedés par FamilleEmploiSeeder
    const INFORMATIQUE = '001';
    const SECRETARIAT = '002';
    const JURIDIQUE = '003';
    const FINANCE = '004';
    const COMMUNICATION = '005';
    const RESSOURCES_HUMAINES = '006';
    const ADMINISTRATION_GENERALE = '007';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function emplois()
    {
        return $this->hasMany(Emploi::class, 'famille_emploi_id');
    }
}
