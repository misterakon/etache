<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sens', 'numero', 'annee', 'sequence', 'date_reception', 'date_courrier', 'reference_courrier', 'expediteur', 'objet',
    'type_courrier_id', 'urgence_id', 'confidentialite', 'origine', 'mode_transmission', 'mode_transmission_precision', 'nombre_pieces', 'observations', 'statut', 'enregistre_par',
    'a_conserver_jusqu_au', 'archive_le', 'numero_archives', 'archive_par',
])]
class Courrier extends Model
{
    protected $table = 'courriers';

    const SENS_ARRIVEE = 'arrivee';
    const SENS_DEPART = 'depart';

    const NORMAL = 'normal';
    const CONFIDENTIEL = 'confidentiel';

    const ORIGINES = ['service_courrier' => 'Service courrier', 'secretariat_dg' => 'Secrétariat du DG'];

    /** Mode de transmission (fiche de transmission du courrier). */
    const MODES_TRANSMISSION = [
        'porteur_coursier' => 'Porteur / coursier (remise en main propre)',
        'courrier_electronique' => 'Courrier électronique',
        'autre' => 'Autre',
    ];

    /** Statut => [libellé, couleur]. Seul « enregistre » est utilisé à l'étape 1 ; les autres viennent avec l'imputation et la réponse. */
    const STATUTS = [
        'enregistre' => ['À imputer', '#5B6B84'],
        'impute' => ['Imputé', '#2B6CB0'],
        'en_traitement' => ['En traitement', '#7B6BE0'],
        'solde' => ['Traité', '#2E7D5B'],   // vocabulaire du registre papier (« Traiter »)
        'archive' => ['Classé', '#8A94A6'], // vocabulaire du registre papier (« Classer »)
    ];

    protected function casts(): array
    {
        return [
            'date_reception' => 'date',
            'date_courrier' => 'date',
            'a_conserver_jusqu_au' => 'date',
            'archive_le' => 'datetime',
        ];
    }

    public function type()
    {
        return $this->belongsTo(TypeCourrier::class, 'type_courrier_id');
    }

    public function urgence()
    {
        return $this->belongsTo(UrgenceCourrier::class, 'urgence_id');
    }

    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /** Scan actuel (une seule version active ; les versions précédentes sont conservées). */
    public function scans()
    {
        return $this->morphMany(PieceJointe::class, 'attachable')->where('categorie', 'scan')->where('actuelle', true);
    }

    public function imputations()
    {
        return $this->hasMany(Imputation::class)->orderBy('date_imputation')->orderBy('id');
    }

    public function diligences()
    {
        return $this->hasMany(Diligence::class)->orderBy('id');
    }

    public function reponses()
    {
        return $this->hasMany(ReponseCourrier::class)->orderBy('id');
    }

    public function departs()
    {
        return $this->hasMany(CourrierDepart::class)->orderBy('id');
    }

    public function scan(): ?PieceJointe
    {
        return $this->scans->first();
    }

    public function estConfidentiel(): bool
    {
        return $this->confidentialite === self::CONFIDENTIEL;
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut][0] ?? $this->statut;
    }

    public function couleurStatut(): string
    {
        return self::STATUTS[$this->statut][1] ?? '#5B6B84';
    }

    public function libelleModeTransmission(): string
    {
        if (! $this->mode_transmission) {
            return '—';
        }
        $libelle = self::MODES_TRANSMISSION[$this->mode_transmission] ?? $this->mode_transmission;

        return $this->mode_transmission === 'autre' && $this->mode_transmission_precision ? "Autre : {$this->mode_transmission_precision}" : $libelle;
    }

    public function libelleOrigine(): string
    {
        return self::ORIGINES[$this->origine] ?? $this->origine;
    }

    public function scopeArrivee(Builder $query): Builder
    {
        return $query->where('sens', self::SENS_ARRIVEE);
    }

    /**
     * Courriers qu'un utilisateur peut voir :
     *  - sans accès au registre : aucun ;
     *  - ADMIN et DG : tous ;
     *  - autres profils du registre (ex. COURRIER) : les courriers non confidentiels, et les confidentiels qu'ils ont enregistrés.
     * (Les « personnes désignées » par une imputation s'ajouteront à l'étape 2.)
     */
    public function scopeVisiblePar(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN|DG')) {
            return $query;
        }

        $peutConsulterRegistre = \App\Support\CourrierDroits::peutConsulterRegistre($user);
        $clesCibles = $user->personnel_id
            ? \App\Support\DestinatairesImputation::clesAssocieesAuPersonnel((int) $user->personnel_id)
            : [];

        return $query->where(function ($visible) use ($user, $peutConsulterRegistre, $clesCibles) {
            if ($peutConsulterRegistre) {
                $visible->where(fn ($q) => $q->where('confidentialite', self::NORMAL)->orWhere('enregistre_par', $user->id));
            } else {
                $visible->whereRaw('1 = 0');
            }

            if ($clesCibles !== []) {
                $visible->orWhereHas('imputations.destinatairesAssocies', function ($destinataires) use ($clesCibles) {
                    $destinataires->whereIn('role', ['avis', 'copie'])->where(function ($cibles) use ($clesCibles) {
                        foreach ($clesCibles as $cle) {
                            [$type, $id] = explode(':', $cle, 2);
                            $cibles->orWhere(fn ($cible) => $cible->where('destinataire_type', $type)->where('destinataire_id', $id));
                        }
                    });
                });
            }
        });
    }
}
