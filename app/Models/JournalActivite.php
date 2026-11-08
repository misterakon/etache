<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Journal d'activité unifié : une entrée par acte significatif, NON modifiable et NON supprimable.
 * Écriture via App\Support\Journal::noter().
 */
#[Fillable([
    'user_id', 'acteur_nom', 'module', 'evenement', 'sujet_type', 'sujet_id',
    'parent_type', 'parent_id', 'resume', 'details', 'ip',
])]
class JournalActivite extends Model
{
    protected $table = 'journal_activites';

    public $timestamps = false; // seule created_at existe

    public const MODULES = [
        'taches' => 'Tâches',
        'sous_taches' => 'Sous-tâches',
        'fichiers' => 'Fichiers',
        'exercices' => 'Exercices',
        'exports' => 'Exports',
        'reglages' => 'Réglages',
        'courrier' => 'Courrier',
        'personnel' => 'Personnel',
        'pilotage' => 'Pilotage',
    ];

    public const EVENEMENTS = [
        'creation' => 'Création',
        'modification' => 'Modification',
        'suppression' => 'Suppression',
        'coche' => 'Coche',
        'decoche' => 'Décoche',
        'depot' => 'Dépôt',
        'remplacement' => 'Remplacement',
        'telechargement' => 'Téléchargement',
        'export' => 'Export',
        'changement_mode_avancement' => "Changement du mode d'avancement",
        'cloture' => 'Clôture',
        'reouverture' => 'Réouverture',
        'reconduction' => 'Reconduction',
        'retablissement' => 'Rétablissement',
        'enregistrement' => 'Enregistrement',
        'consultation' => 'Consultation',
        'scan' => 'Scan',
        'imputation' => 'Imputation',
        'affectation' => 'Affectation',
        'fin_affectation' => "Fin d'affectation",
        'dossier' => 'Dossier',
        'prise_en_charge' => 'Prise en charge',
        'solde' => 'Traitée',
        'avis_diligence' => 'Avis',
        'reimputation' => 'Réimputation',
        'suivi_responsable' => 'Changement de responsable',
        'renvoi' => 'Renvoi au supérieur',
        'classement' => 'Classement',
        'validation' => 'Validation',
        'reponse' => 'Réponse',
        'depart' => 'Départ',
        'archivage' => 'Archivage',
        'restauration' => 'Restauration',
        'import' => 'Import',
        'synthese' => 'Synthèse',
        'delai' => 'Délai',
        'traitement' => 'Traitement',
        'refus' => 'Refus',
        'annulation' => 'Annulation',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $e) => $e->created_at ??= now());
        static::updating(fn () => throw new LogicException('Le journal est non modifiable.'));
        static::deleting(fn () => throw new LogicException('Le journal est non supprimable.'));
    }

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'details' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libelleModule(): string
    {
        return self::MODULES[$this->module] ?? $this->module;
    }

    public function libelleEvenement(): string
    {
        return self::EVENEMENTS[$this->evenement] ?? $this->evenement;
    }

    /** Activité d'une tâche (sous-tâches, fichiers...) pour l'onglet "Activité" ; les téléchargements n'y figurent pas. */
    public static function pourTache(Tache $tache)
    {
        return static::where('parent_type', $tache->getMorphClass())
            ->where('parent_id', $tache->id)
            ->where('evenement', '!=', 'telechargement')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();
    }
}
