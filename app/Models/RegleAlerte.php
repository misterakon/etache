<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'libelle', 'decalage_jours', 'destinataires', 'actif'])]
class RegleAlerte extends Model
{
    protected $table = 'regles_alerte';

    // Cles stockees dans la colonne `destinataires` (liste JSON). Le moteur d'alertes les traduit en personnes :
    //  - responsable / pilote / contributeur : agents affectes a la tache selon leur role d'affectation ;
    //  - responsable_direction : utilisateur de profil RD rattache a la direction du responsable ;
    //  - dg / administrateur : utilisateurs de profil DG / ADMIN.
    const RESPONSABLE = 'responsable';
    const PILOTE = 'pilote';
    const CONTRIBUTEUR = 'contributeur';
    const RESPONSABLE_DIRECTION = 'responsable_direction';
    const DG = 'dg';
    const ADMINISTRATEUR = 'administrateur';

    const DESTINATAIRES = [
        self::RESPONSABLE => 'Responsable de la tâche',
        self::PILOTE => 'Pilote',
        self::CONTRIBUTEUR => 'Contributeurs',
        self::RESPONSABLE_DIRECTION => 'Responsable de direction',
        self::DG => 'Directeur Général',
        self::ADMINISTRATEUR => 'Administrateur',
    ];

    protected function casts(): array
    {
        return [
            'decalage_jours' => 'integer',
            'destinataires' => 'array',
            'actif' => 'boolean',
        ];
    }

    /**
     * Libelles lisibles des destinataires de la regle (pour l'affichage), ex. ['Responsable de la tâche', 'Pilote'].
     */
    public function libellesDestinataires(): array
    {
        return collect($this->destinataires ?? [])
            ->map(fn ($cle) => self::DESTINATAIRES[$cle] ?? $cle)
            ->values()
            ->all();
    }

    public function canaux()
    {
        return $this->belongsToMany(CanalNotification::class, 'regle_alerte_canal')
            ->withTimestamps();
    }

    public function alertesEnvoyees()
    {
        return $this->hasMany(AlerteEnvoyee::class);
    }
}
