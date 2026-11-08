<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'couleur', 'actif'])]
class SituationTemporelle extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'situations_temporelles';

    // Codes seedés par SituationTemporelleSeeder
    const NORMAL = '001';
    const ECHEANCE_PROCHE = '002';
    const A_RISQUE = '003';
    const EN_RETARD = '004';
    const TERMINEE = '005';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'situation_temporelle_id');
    }
}
