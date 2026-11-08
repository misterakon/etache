<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'service_id', 'type_agent_id', 'emploi_id',
    'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance',
    'fonction', 'email', 'telephone', 'lieu_habitation', 'photo', 'actif', 'fonction_id',
    'genre_id', 'civilite_id', 'statut_matrimonial_id', 'nombre_enfants', 'nom_pere', 'nom_mere', 'telephone_2',
    'grade_id', 'classe_id', 'echelon_id',
    'date_premiere_prise_service', 'date_prise_service', 'date_depart_retraite', 'date_deces',
    'archive_le', 'archive_motif', 'archive_par',
])]
class Personnel extends Model
{
    protected $table = 'personnel';

    // Ajoute photo_url au JSON (utilise par le formulaire de modification)
    protected $appends = ['photo_url'];

    /**
     * URL publique de la photo, ou null si l'agent n'en a pas
     * (les vues affichent alors un avatar a initiales).
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->photo ? asset('uploads/personnel/'.$this->photo) : null
        );
    }

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'actif' => 'boolean', 'archive_le' => 'datetime',
            'date_premiere_prise_service' => 'date', 'date_prise_service' => 'date', 'date_depart_retraite' => 'date', 'date_deces' => 'date',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function typeAgent()
    {
        return $this->belongsTo(TypeAgent::class);
    }

    public function emploi()
    {
        return $this->belongsTo(Emploi::class);
    }

    public function civilite() { return $this->belongsTo(ListeValeur::class, 'civilite_id'); }
    public function genre() { return $this->belongsTo(ListeValeur::class, 'genre_id'); }
    public function statutMatrimonial() { return $this->belongsTo(ListeValeur::class, 'statut_matrimonial_id'); }
    public function grade() { return $this->belongsTo(Grade::class); }
    public function classeAgent() { return $this->belongsTo(ClasseAgent::class, 'classe_id'); }
    public function echelon() { return $this->belongsTo(Echelon::class); }

    public function competences()
    {
        return $this->hasMany(PersonnelCompetence::class)->orderBy('libelle');
    }

    public function formations()
    {
        return $this->hasMany(PersonnelFormation::class)->orderByDesc('date_debut')->orderByDesc('id');
    }

    /** Un agent archivé n'est jamais supprimé : il quitte les listes courantes, son historique reste. */
    public function estArchive(): bool
    {
        return $this->archive_le !== null;
    }

    public function scopeNonArchives($query)
    {
        return $query->whereNull('archive_le');
    }

    public function scopeArchives($query)
    {
        return $query->whereNotNull('archive_le');
    }

    /**
     * Rattachement réel : service, sous-direction et direction déduits du service de l'agent OU des postes qu'il occupe
     * (un directeur n'a pas de service : sa direction vient de son poste).
     *
     * @return array{direction: ?Direction, sous_direction: ?SousDirection, service: ?Service}
     */
    public function rattachement(): array
    {
        $service = $this->service;
        $sousDirection = $service?->sousDirection;
        $direction = $service?->direction();

        foreach ($this->mandats()->actifA()->with(['direction', 'sousDirection.direction', 'service.sousDirection.direction'])->get() as $mandat) {
            match ($mandat->niveau) {
                MandatResponsable::NIVEAU_SERVICE => $service ??= $mandat->service,
                MandatResponsable::NIVEAU_SOUS_DIRECTION => $sousDirection ??= $mandat->sousDirection,
                MandatResponsable::NIVEAU_DIRECTION => $direction ??= $mandat->direction,
                default => null,
            };
        }
        $sousDirection ??= $service?->sousDirection;
        $direction ??= $sousDirection?->direction ?? $service?->direction();

        return ['direction' => $direction, 'sous_direction' => $sousDirection, 'service' => $service];
    }

    public function fonctionDef()
    {
        return $this->belongsTo(Fonction::class, 'fonction_id');
    }

    /** Postes occupés aujourd'hui (mandats en cours) : [['libelle' => ..., 'interim' => bool], ...] */
    public function postesActuels(): array
    {
        $mandats = $this->relationLoaded('mandats')
            ? $this->mandats
            : $this->mandats()->actifA()->with(['direction', 'sousDirection', 'service'])->get();

        return $mandats->map(fn ($m) => [
            'id' => $m->id,
            'libelle' => $m->libellePoste(),
            'interim' => $m->type === MandatResponsable::INTERIM,
            'debut_iso' => $m->date_debut->format('Y-m-d'),
        ])->values()->all();
    }
    
    /**
     * Le compte applicatif lié, s'il en a un (pas systématique).
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Toutes les affectations de cet agent (responsable/pilote/contributeur),
     * à tout niveau de la hiérarchie (ActionCle, ActionMesurable ou Tache).
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function alertesRecues()
    {
        return $this->hasMany(AlerteEnvoyee::class);
    }

    /**
     * Historique des postes de responsable occupes par cet agent (service, sous-direction, direction, DG).
     */
    public function mandats()
    {
        return $this->hasMany(MandatResponsable::class)->orderByDesc('date_debut');
    }

    /**
     * Nom complet, pratique pour l'affichage.
     */
    public function nomComplet(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    /**
     * Initiales pour les avatars : "JB" pour Jean-Baptiste Kouassi ; la premiere lettre du nom
     * seule si le prenom n'est pas renseigne.
     */
    public function initiales(): string
    {
        $prenom = trim((string) $this->prenom);
        $nom = trim((string) $this->nom);

        $premiere = mb_strtoupper(mb_substr($prenom !== '' ? $prenom : $nom, 0, 1));
        $seconde = ($prenom !== '' && $nom !== '') ? mb_strtoupper(mb_substr($nom, 0, 1)) : '';

        return $premiere.$seconde;
    }
}
