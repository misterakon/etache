<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Photo quotidienne des chiffres (global, par axe, par direction) : sert aux courbes d'évolution des retards et de l'avancement. */
#[Fillable(['cle', 'jour', 'exercice_id', 'axe_id', 'direction_id', 'taches', 'terminees', 'en_cours', 'en_retard', 'non_justifies', 'avancement_moyen'])]
class InstantanePilotage extends Model
{
    protected $table = 'instantanes_pilotage';

    protected function casts(): array
    {
        return ['jour' => 'date', 'avancement_moyen' => 'float'];
    }
}
