<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Synthèse du pilotage conservée : rédigée par l'IA, par les modèles de phrases ou saisie/corrigée à la main. */
#[Fillable(['exercice_id', 'periode_type', 'periode_valeur', 'portee_type', 'portee_id', 'contenu', 'source', 'modele', 'empreinte', 'auteur_id'])]
class SynthesePilotage extends Model
{
    protected $table = 'syntheses_pilotage';

    const SOURCES = ['ia' => ['Rédigée par IA', '#7B6BE0'], 'modele' => ['Modèles de phrases', '#0F8B8D'], 'manuel' => ['Corrigée à la main', '#C77D0A']];

    public function exercice()
    {
        return $this->belongsTo(Exercice::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function libelleSource(): string
    {
        return self::SOURCES[$this->source][0] ?? $this->source;
    }

    public function couleurSource(): string
    {
        return self::SOURCES[$this->source][1] ?? '#5B6B84';
    }
}
