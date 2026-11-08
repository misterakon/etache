<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Valeur d'une liste simple du personnel (genre, civilité, statut matrimonial, type de document). Pas d'écran : valeurs en base. */
#[Fillable(['liste', 'code', 'libelle', 'ordre', 'actif'])]
class ListeValeur extends Model
{
    protected $table = 'listes_valeurs';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function scopeDeListe(Builder $query, string $liste): Builder
    {
        return $query->where('liste', $liste)->where('actif', true)->orderBy('ordre');
    }
}
