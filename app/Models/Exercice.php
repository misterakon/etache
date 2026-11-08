<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['annee', 'libelle', 'date_debut', 'date_fin', 'actif', 'mode_avancement', 'cloture_le', 'cloture_par', 'cloture_archive'])]
class Exercice extends Model
{
    protected $table = 'exercices';
    const MODE_MANUEL = 'manuel';
    const MODE_SOUS_TACHES = 'sous_taches';

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'actif' => 'boolean',
            'cloture_le' => 'datetime',
        ];
    }

    public function axes()
    {
        return $this->hasMany(Axe::class);
    }

    public function avancementParSousTaches(): bool
    {
        return $this->mode_avancement === self::MODE_SOUS_TACHES;
    }

    /**
     * L'exercice actuellement actif (il ne devrait y en avoir qu'un seul à la fois).
     */
    public static function actif(): ?self
    {
        return static::where('actif', true)->first();
    }

    /**
     * Exercice affiche par defaut dans toute l'application : celui de l'annee en cours ET actif ;
     * a defaut, l'exercice actif (meme si son annee n'est pas l'annee en cours) ; sinon null (tout afficher).
     */
    public static function parDefaut(): ?self
    {
        return static::where('annee', now()->year)->where('actif', true)->first()
            ?? static::actif();
    }

    /**
     * Les deux derniers chiffres de l'année, pour les codes (AM26-001, TA26-001...).
     */
    public function anneeCourte(): string
    {
        return substr((string) $this->annee, -2);
    }

    public function estClos(): bool
    {
        return $this->cloture_le !== null;
    }

    public function cloturePar()
    {
        return $this->belongsTo(User::class, 'cloture_par');
    }
}
