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
        'taches_ponctuelles' => 'Tâches ponctuelles',
        'fichiers' => 'Fichiers',
        'exercices' => 'Exercices',
        'exports' => 'Exports',
        'reglages' => 'Réglages',
        'parametre' => 'Paramètres',
        'courrier' => 'Courrier',
        'personnel' => 'Personnel',
        'pilotage' => 'Pilotage',
        'session' => 'Session',
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
        'verrouillage' => 'Verrouillage',
        'deverrouillage' => 'Déverrouillage',
        'deverrouillage_echec' => 'Échec de déverrouillage',
        'deconnexion_inactivite' => 'Déconnexion (inactivité)',
        'mot_de_passe_modifie' => 'Mot de passe modifié',
        'profil_acces_cree' => "Création d'un profil d'accès",
        'profil_acces_modifie' => "Modification d'un profil d'accès",
        'profil_acces_supprime' => "Suppression d'un profil d'accès",
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

    /** Ton (couleur de la palette commune, voir journal-alertes.css) déduit du type d'événement. */
    public function ton(): string
    {
        return match (true) {
            in_array($this->evenement, ['suppression', 'refus', 'annulation', 'verrouillage', 'deverrouillage_echec', 'deconnexion_inactivite', 'fin_affectation', 'profil_acces_supprime'], true) => 'retard',
            in_array($this->evenement, ['creation', 'validation', 'deverrouillage', 'restauration', 'reouverture', 'enregistrement', 'depot', 'profil_acces_cree'], true) => 'terminee',
            in_array($this->evenement, ['consultation', 'telechargement', 'export', 'scan'], true) => 'neutre',
            default => 'encours',
        };
    }

    /** Icône Font Awesome déduite du type d'événement. */
    public function icone(): string
    {
        return match (true) {
            str_contains($this->evenement, 'suppression') => 'fa-solid fa-trash-can',
            str_contains($this->evenement, 'creation') => 'fa-solid fa-plus',
            str_contains($this->evenement, 'modification') => 'fa-regular fa-pen-to-square',
            $this->evenement === 'verrouillage' => 'fa-solid fa-lock',
            str_contains($this->evenement, 'deverrouillage') => 'fa-solid fa-lock-open',
            str_contains($this->evenement, 'validation') => 'fa-solid fa-check',
            str_contains($this->evenement, 'refus'), str_contains($this->evenement, 'annulation') => 'fa-solid fa-xmark',
            str_contains($this->evenement, 'telechargement') => 'fa-solid fa-download',
            str_contains($this->evenement, 'export') => 'fa-solid fa-file-export',
            str_contains($this->evenement, 'import') => 'fa-solid fa-file-import',
            str_contains($this->evenement, 'consultation') => 'fa-regular fa-eye',
            str_contains($this->evenement, 'depot') => 'fa-solid fa-upload',
            str_contains($this->evenement, 'archivage') => 'fa-solid fa-box-archive',
            str_contains($this->evenement, 'restauration') => 'fa-solid fa-rotate-left',
            str_contains($this->evenement, 'cloture') => 'fa-solid fa-lock',
            str_contains($this->evenement, 'reouverture') => 'fa-solid fa-lock-open',
            str_contains($this->evenement, 'affectation') => 'fa-solid fa-user-tag',
            str_contains($this->evenement, 'imputation'), str_contains($this->evenement, 'renvoi') => 'fa-solid fa-share',
            str_contains($this->evenement, 'reponse') => 'fa-solid fa-reply',
            str_contains($this->evenement, 'synthese') => 'fa-solid fa-file-lines',
            str_contains($this->evenement, 'mot_de_passe') => 'fa-solid fa-key',
            str_contains($this->evenement, 'deconnexion') => 'fa-solid fa-right-from-bracket',
            default => 'fa-solid fa-circle-dot',
        };
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
