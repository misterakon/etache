<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Échelle de traitement : indice d'une combinaison grade + classe + échelon (la combinaison doit exister). */
#[Fillable(['grade_id', 'classe_id', 'echelon_id', 'indice'])]
class EchelleTraitement extends Model
{
    protected $table = 'echelle_traitements';

    public $timestamps = false;

    public static function indiceDe(?int $gradeId, ?int $classeId, ?int $echelonId): ?int
    {
        if (! $gradeId || ! $classeId || ! $echelonId) {
            return null;
        }

        return static::where(['grade_id' => $gradeId, 'classe_id' => $classeId, 'echelon_id' => $echelonId])->value('indice');
    }
}
