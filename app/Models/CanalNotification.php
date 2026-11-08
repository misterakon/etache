<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'actif'])]
class CanalNotification extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'canaux_notification';

    // Codes seedés par CanalNotificationSeeder
    const APPLICATION = '001';
    const EMAIL = '002';
    const SMS = '003';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function reglesAlerte()
    {
        return $this->belongsToMany(RegleAlerte::class, 'regle_alerte_canal')
            ->withTimestamps();
    }

    public function alertesEnvoyees()
    {
        return $this->hasMany(AlerteEnvoyee::class, 'canal_notification_id');
    }
}
