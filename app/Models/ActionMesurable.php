<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeAnnuel;
use App\Models\Concerns\ProtegeParCloture;

#[Fillable(['code', 'action_cle_id', 'libelle', 'description', 'ordre'])]
class ActionMesurable extends Model
{
    use GenereCodeAnnuel;
    use ProtegeParCloture;

    protected $table = 'actions_mesurables';
    protected static string $prefixeCode = 'AM';
    protected static int $largeurCode = 3; // AM26-001, AM26-002...

    public function anneeCode(): string
    {
        $exercice = $this->actionCle?->axe?->exercice;

        return $exercice ? $exercice->anneeCourte() : now()->format('y');
    }

    public function actionCle()
    {
        return $this->belongsTo(ActionCle::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class)->orderBy('ordre');
    }

    /**
     * Affectations (responsable/pilote/contributeur) directement sur cette action mesurable.
     */
    public function affectations()
    {
        return $this->morphMany(Affectation::class, 'affectable');
    }

    public function exercicesProteges(): array
    {
        return [$this->actionCle?->axe?->exercice];
    }
}
