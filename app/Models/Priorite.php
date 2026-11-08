<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'couleur', 'actif'])]
class Priorite extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'priorites';

    // Codes seedés par PrioriteSeeder
    const HAUTE = '001';
    const MOYENNE = '002';
    const BASSE = '003';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'priorite_id');
    }
}
