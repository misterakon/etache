<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'libelle', 'categorie', 'niveau_mandat', 'titulaire_unique', 'systeme', 'ordre', 'actif'])]
class Fonction extends Model
{
    protected $table = 'fonctions';

    const CAT_MANDAT = 'mandat';
    const CAT_SERVICE = 'service';
    const CAT_DG = 'direction_generale';

    const CATEGORIES = [
        self::CAT_MANDAT => 'Poste à titulaire unique (mandat)',
        self::CAT_SERVICE => 'Rattaché à un service',
        self::CAT_DG => 'Rattaché à la Direction Générale',
    ];

    protected function casts(): array
    {
        return ['titulaire_unique' => 'boolean', 'systeme' => 'boolean', 'actif' => 'boolean'];
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }

    public function estMandat(): bool
    {
        return $this->categorie === self::CAT_MANDAT;
    }
}
