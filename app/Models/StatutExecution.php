<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'couleur', 'actif'])]
class StatutExecution extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'statuts_execution';

    // Codes seedés par StatutExecutionSeeder
    const NON_DEMARREE = '001';
    const EN_COURS = '002';
    const BLOQUEE = '003';
    const SUSPENDUE = '004';
    const TERMINEE = '005';
    const ANNULEE = '006';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'statut_execution_id');
    }
}
