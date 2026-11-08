<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Dates d'un document du dossier d'un agent : date du document et fin de validité (rappels d'échéance). */
#[Fillable(['piece_jointe_id', 'date_document', 'valide_jusqu_au', 'observation', 'modifie_par'])]
class DossierInfo extends Model
{
    protected $table = 'dossier_infos';

    protected function casts(): array
    {
        return ['date_document' => 'date', 'valide_jusqu_au' => 'date'];
    }

    public function piece()
    {
        return $this->belongsTo(PieceJointe::class, 'piece_jointe_id')->withTrashed();
    }

    /** valide | bientot (dans le délai de rappel) | expire | sans_date */
    public function etat(?int $joursRappel = null): string
    {
        if (! $this->valide_jusqu_au) {
            return 'sans_date';
        }
        $joursRappel ??= (int) \App\Support\Reglages::get('personnel.rappel_dossier_jours');

        return match (true) {
            $this->valide_jusqu_au->lt(today()) => 'expire',
            $this->valide_jusqu_au->lte(today()->addDays($joursRappel)) => 'bientot',
            default => 'valide',
        };
    }
}
