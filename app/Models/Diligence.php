<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Diligence : le travail demandé à un destinataire pour un courrier imputé.
 * Statuts stockés : a_prendre_en_charge, en_cours, solde, annule. « En retard » se calcule (échéance dépassée, non soldée).
 */
#[Fillable([
    'code', 'courrier_id', 'imputation_id', 'parent_diligence_id', 'destinataire_type', 'destinataire_id', 'destinataire_libelle',
    'responsable_personnel_id', 'role', 'echeance', 'statut', 'pris_en_charge_le', 'pris_en_charge_par', 'solde_le', 'solde_par',
    'commentaire_solde', 'annule_le', 'motif_annulation',
])]
class Diligence extends Model
{
    protected $table = 'diligences';

    const STATUTS = [
        'a_prendre_en_charge' => ['À prendre en charge', '#C77D0A'],
        'en_cours' => ['En cours', '#2B6CB0'],
        'en_retard' => ['En retard', '#A73B34'],
        'reimputee' => ['Réimputée', '#7B6BE0'],
        'solde' => ['Traitée', '#2E7D5B'],
        'annule' => ['Annulée', '#8A94A6'],
    ];

    const ROLES = ['principal' => 'Responsable principal', 'avis' => 'Associé pour avis', 'copie' => 'Associé pour copie'];

    protected function casts(): array
    {
        return ['echeance' => 'date', 'pris_en_charge_le' => 'datetime', 'solde_le' => 'datetime', 'annule_le' => 'datetime'];
    }

    public function courrier()
    {
        return $this->belongsTo(Courrier::class);
    }

    public function imputation()
    {
        return $this->belongsTo(Imputation::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Personnel::class, 'responsable_personnel_id');
    }

    public function diligenceParente()
    {
        return $this->belongsTo(self::class, 'parent_diligence_id');
    }

    public function diligencesEnfants()
    {
        return $this->hasMany(self::class, 'parent_diligence_id');
    }

    public function actions()
    {
        return $this->hasMany(DiligenceAction::class)->orderBy('ordre')->orderBy('id');
    }

    public function delais()
    {
        return $this->hasMany(DiligenceDelai::class)->orderByDesc('id');
    }

    public function reponses()
    {
        return $this->hasMany(ReponseCourrier::class)->orderBy('id');
    }

    /** Avancement du plan de traitement : seules les actions validées comptent. */
    public function avancementActions(): array
    {
        $validees = $this->actions()->validees()->get();
        $total = $validees->count();
        $faites = $validees->where('faite', true)->count();

        return ['total' => $total, 'faites' => $faites, 'pourcentage' => $total ? (int) round($faites / $total * 100) : 0];
    }

    public function avisAssocies()
    {
        return $this->hasMany(ImputationDestinataire::class)->where('role', 'avis')->with('avis');
    }

    public function estOuverte(): bool
    {
        return in_array($this->statut, ['a_prendre_en_charge', 'en_cours'], true);
    }

    public function estEnRetard(): bool
    {
        return $this->estOuverte() && $this->echeance->lt(today());
    }

    /** Statut affiché : « en retard » prend le pas sur « à prendre en charge » et « en cours ». */
    public function statutAffiche(): string
    {
        return $this->estEnRetard() ? 'en_retard' : $this->statut;
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statutAffiche()][0] ?? $this->statut;
    }

    public function couleurStatut(): string
    {
        return self::STATUTS[$this->statutAffiche()][1] ?? '#5B6B84';
    }

    public function libelleRole(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function scopeOuvertes($query)
    {
        return $query->whereIn('statut', ['a_prendre_en_charge', 'en_cours']);
    }

    /** Diligences vivantes : ouvertes, ou transmises mais pas encore revenues. */
    public function scopeActives($query)
    {
        return $query->whereIn('statut', ['a_prendre_en_charge', 'en_cours', 'reimputee']);
    }

    /** Bout de la chaîne (qui a la main) et échéance ouverte la plus proche. */
    public function suivi(): array
    {
        $bout = $this;
        $echeance = $this->estOuverte() ? $this->echeance : null;
        while ($enfant = $bout->diligencesEnfants()->ouvertes()->orderBy('id')->first()) {
            $bout = $enfant;
            $echeance = $echeance ? $echeance->min($enfant->echeance) : $enfant->echeance;
        }

        return ['bout' => $bout, 'echeance' => $echeance ?? $this->echeance];
    }
}
