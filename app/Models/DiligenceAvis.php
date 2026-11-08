<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['imputation_destinataire_id', 'user_id', 'commentaire'])]
class DiligenceAvis extends Model
{
    protected $table = 'diligence_avis';

    public function destinataireAssocie()
    {
        return $this->belongsTo(ImputationDestinataire::class, 'imputation_destinataire_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
