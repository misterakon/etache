<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tache_id', 'user_id', 'champ', 'ancienne_valeur', 'nouvelle_valeur'])]
class TacheHistorique extends Model
{
    protected $table = 'tache_historiques';

    // La table n'a qu'une colonne created_at (pas d'updated_at) : pas de gestion auto des timestamps.
    public $timestamps = false;

    protected $attributes = [
        // rempli explicitement à la création, useCurrent() sert de filet de sécurité côté DB
    ];

    protected static function booted(): void
    {
        static::creating(function (self $historique) {
            $historique->created_at ??= now();
        });
    }

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
