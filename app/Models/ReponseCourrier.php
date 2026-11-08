<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Réponse à un courrier : projet joint, circuit de visas montant la hiérarchie, signature scannée. */
#[Fillable(['courrier_id', 'diligence_id', 'objet', 'commentaire', 'statut', 'cycle', 'soumis_le', 'signe_le', 'signe_par', 'cree_par'])]
class ReponseCourrier extends Model
{
    protected $table = 'reponses_courrier';

    const STATUTS = [
        'brouillon' => ['Brouillon', '#5B6B84'],
        'en_visa' => ['En circuit de visa', '#2B6CB0'],
        'a_corriger' => ['À corriger', '#C77D0A'],
        'signee' => ['Signée', '#2E7D5B'],
        'annulee' => ['Annulée', '#8A94A6'],
    ];

    protected function casts(): array
    {
        return ['soumis_le' => 'datetime', 'signe_le' => 'datetime'];
    }

    public function courrier()
    {
        return $this->belongsTo(Courrier::class);
    }

    public function diligence()
    {
        return $this->belongsTo(Diligence::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function signataire()
    {
        return $this->belongsTo(User::class, 'signe_par');
    }

    public function visas()
    {
        return $this->hasMany(ReponseVisa::class, 'reponse_id')->orderBy('cycle')->orderBy('ordre');
    }

    /** Visas du circuit en cours (les cycles précédents restent dans l'historique). */
    public function visasDuCycle()
    {
        return $this->visas->where('cycle', $this->cycle)->values();
    }

    public function visaCourant(): ?ReponseVisa
    {
        return $this->statut === 'en_visa' ? $this->visasDuCycle()->firstWhere('statut', 'en_attente') : null;
    }

    public function fichiers()
    {
        return $this->morphMany(PieceJointe::class, 'attachable')->where('actuelle', true);
    }

    public function projet(): ?PieceJointe
    {
        return $this->fichiers->firstWhere('categorie', 'projet');
    }

    public function signee(): ?PieceJointe
    {
        return $this->fichiers->firstWhere('categorie', 'signature');
    }

    public function depart()
    {
        return $this->hasOne(CourrierDepart::class, 'reponse_id')->orderBy('id');
    }

    public function estModifiable(): bool
    {
        return in_array($this->statut, ['brouillon', 'a_corriger'], true);
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut][0] ?? $this->statut;
    }

    public function couleurStatut(): string
    {
        return self::STATUTS[$this->statut][1] ?? '#5B6B84';
    }
}
