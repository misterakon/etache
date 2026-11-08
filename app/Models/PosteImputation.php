<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['libelle', 'niveau', 'unite_type', 'unite_id', 'personnel_id', 'peut_reimputer', 'ordre', 'actif'])]
class PosteImputation extends Model
{
    protected $table = 'postes_imputation';

    protected function casts(): array
    {
        return ['actif' => 'boolean', 'peut_reimputer' => 'boolean'];
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function titulaireActuel()
    {
        if (! $this->niveau) {
            return $this->personnel;
        }

        return $this->mandatActuel()?->personnel;
    }

    public function mandatActuel(): ?MandatResponsable
    {
        return $this->niveau ? MandatResponsable::enPoste($this->niveau, $this->unite_id) : null;
    }

    public function libelleActuel(): string
    {
        if (! $this->niveau) {
            return $this->libelle;
        }

        return match ($this->niveau) {
            MandatResponsable::NIVEAU_DG => 'Directeur Général',
            MandatResponsable::NIVEAU_DGA => 'Directeur Général Adjoint',
            MandatResponsable::NIVEAU_DIRECTION => 'Directeur — '.Direction::find($this->unite_id)?->nom,
            MandatResponsable::NIVEAU_SOUS_DIRECTION => 'Sous-directeur — '.SousDirection::find($this->unite_id)?->nom,
            MandatResponsable::NIVEAU_SERVICE => 'Chef de service — '.Service::find($this->unite_id)?->nom,
            default => $this->libelle,
        };
    }
}
