<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['imputation_id', 'diligence_id', 'destinataire_type', 'destinataire_id', 'destinataire_libelle', 'role'])]
class ImputationDestinataire extends Model
{
    protected $table = 'imputation_destinataires';

    public function imputation()
    {
        return $this->belongsTo(Imputation::class);
    }

    public function diligence()
    {
        return $this->belongsTo(Diligence::class);
    }

    public function avis()
    {
        return $this->hasMany(DiligenceAvis::class)->with('auteur')->latest();
    }
}
