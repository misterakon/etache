<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Courrier départ : une ligne par destinataire (un numéro D26-0001 chacun, comme le registre papier).
 * Les lignes d'un même envoi partagent un « groupe » (objet saisi une fois, scan joint à la première ligne).
 */
#[Fillable([
    'groupe', 'numero', 'annee', 'sequence', 'courrier_id', 'reponse_id', 'diligence_id', 'destinataire', 'destinataire_precision',
    'objet', 'date_depart', 'mode_envoi', 'mode_envoi_precision', 'nombre_pieces', 'confidentialite', 'observations',
    'numero_archives', 'a_conserver_jusqu_au', 'archive_le', 'archive_par', 'statut', 'enregistre_par',
])]
class CourrierDepart extends Model
{
    protected $table = 'departs';

    const STATUTS = [
        'enregistre' => ['Enregistré', '#2B6CB0'],
        'archive' => ['Classé', '#8A94A6'],
    ];

    protected function casts(): array
    {
        return ['date_depart' => 'date', 'a_conserver_jusqu_au' => 'date', 'archive_le' => 'datetime'];
    }

    public function courrier()
    {
        return $this->belongsTo(Courrier::class);
    }

    public function reponse()
    {
        return $this->belongsTo(ReponseCourrier::class, 'reponse_id');
    }

    public function diligence()
    {
        return $this->belongsTo(Diligence::class);
    }

    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /** Les autres lignes du même envoi. */
    public function groupeComplet()
    {
        return static::where('groupe', $this->groupe)->orderBy('id');
    }

    public function estConfidentiel(): bool
    {
        return $this->confidentialite === Courrier::CONFIDENTIEL;
    }

    /** Scan de l'envoi (joint à la première ligne du groupe) ; à défaut, la réponse signée à l'origine du départ. */
    public function scan(): ?PieceJointe
    {
        $ids = static::where('groupe', $this->groupe)->pluck('id');
        $scan = PieceJointe::where('attachable_type', $this->getMorphClass())->whereIn('attachable_id', $ids)
            ->where('categorie', 'scan')->where('actuelle', true)->first();

        return $scan ?? $this->reponse?->signee();
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut][0] ?? $this->statut;
    }

    public function couleurStatut(): string
    {
        return self::STATUTS[$this->statut][1] ?? '#5B6B84';
    }

    public function libelleModeEnvoi(): string
    {
        if (! $this->mode_envoi) {
            return '—';
        }
        $libelle = Courrier::MODES_TRANSMISSION[$this->mode_envoi] ?? $this->mode_envoi;

        return $this->mode_envoi === 'autre' && $this->mode_envoi_precision ? "Autre : {$this->mode_envoi_precision}" : $libelle;
    }

    /** Un départ confidentiel reste réservé à l'ADMIN, au DG et à la personne qui l'a enregistré. */
    public function scopeVisiblePar(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN|DG')) {
            return $query;
        }

        return $query->where(fn ($q) => $q->where('confidentialite', Courrier::NORMAL)->orWhere('enregistre_par', $user->id));
    }
}
