<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tache_id', 'regle_alerte_id', 'canal_notification_id', 'personnel_id', 'echeance_au_envoi', 'envoye_le'])]
class AlerteEnvoyee extends Model
{
    protected $table = 'alertes_envoyees';

    protected function casts(): array
    {
        return [
            'envoye_le' => 'datetime',
            'echeance_au_envoi' => 'date', // echeance de la tache au moment de l'envoi (anti-doublon)
        ];
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function regleAlerte()
    {
        return $this->belongsTo(RegleAlerte::class);
    }

    public function canalNotification()
    {
        return $this->belongsTo(CanalNotification::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
