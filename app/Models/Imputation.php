<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['courrier_id', 'parent_diligence_id', 'imputee_par', 'date_imputation', 'delai_id', 'delai_libelle', 'echeance', 'instructions_particulieres', 'observations'])]
class Imputation extends Model
{
    protected $table = 'imputations';

    protected function casts(): array
    {
        return ['date_imputation' => 'date', 'echeance' => 'date'];
    }

    public function courrier()
    {
        return $this->belongsTo(Courrier::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'imputee_par');
    }

    public function instructions()
    {
        return $this->belongsToMany(InstructionCourrier::class, 'imputation_instruction', 'imputation_id', 'instruction_id')->orderBy('ordre');
    }

    public function diligences()
    {
        return $this->hasMany(Diligence::class);
    }

    public function destinatairesAssocies()
    {
        return $this->hasMany(ImputationDestinataire::class);
    }

    public function diligenceParente()
    {
        return $this->belongsTo(Diligence::class, 'parent_diligence_id');
    }
}
