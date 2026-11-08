<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeAvecPrefixe;
use App\Models\Concerns\ProtegeParCloture;

#[Fillable(['code', 'exercice_id', 'libelle', 'resultat_attendu', 'description', 'ordre'])]
class Axe extends Model
{
    use GenereCodeAvecPrefixe;
    use ProtegeParCloture;

    protected $table = 'axes';
    protected static string $prefixeCode = 'A';
    protected static int $largeurCode = 3; // A001, A002...

    public function exercice()
    {
        return $this->belongsTo(Exercice::class);
    }

    public function actionsCles()
    {
        return $this->hasMany(ActionCle::class)->orderBy('ordre');
    }

    public function exercicesProteges(): array
    {
        return [$this->exercice];
    }
}
