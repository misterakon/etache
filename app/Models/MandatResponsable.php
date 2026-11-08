<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Historique des responsables : une ligne = une personne a la tete d'une unite pendant une periode.
 * Le responsable d'une unite a une date donnee est le mandat actif a cette date
 * (l'interim a priorite sur le titulaire).
 */
#[Fillable([
    'niveau', 'direction_id', 'sous_direction_id', 'service_id', 'personnel_id', 'type',
    'date_debut', 'date_fin', 'reference_acte', 'motif_fin', 'cree_par',
])]
class MandatResponsable extends Model
{
    protected $table = 'mandats_responsables';

    const NIVEAU_DG = 'direction_generale';
    const NIVEAU_DIRECTION = 'direction';
    const NIVEAU_SOUS_DIRECTION = 'sous_direction';
    const NIVEAU_SERVICE = 'service';
    const NIVEAU_DGA = 'dga';

    const TITULAIRE = 'titulaire';
    const INTERIM = 'interim';

    protected function casts(): array
    {
        return ['date_debut' => 'date', 'date_fin' => 'date'];
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function sousDirection()
    {
        return $this->belongsTo(SousDirection::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function libellePoste(): string
    {
        return match ($this->niveau) {
            self::NIVEAU_DG => 'Directeur Général',
            self::NIVEAU_DGA => 'Directeur Général Adjoint',
            self::NIVEAU_DIRECTION => 'Directeur — '.($this->direction?->nom ?? ''),
            self::NIVEAU_SOUS_DIRECTION => 'Sous-directeur — '.($this->sousDirection?->nom ?? ''),
            self::NIVEAU_SERVICE => 'Chef de service — '.($this->service?->nom ?? ''),
            default => $this->niveau,
        };
    }

    /** Nom de l'unite dirigee (necessite les relations direction / sousDirection / service chargees pour eviter des requetes). */
    public function libelleUnite(): string
    {
        return match ($this->niveau) {
            self::NIVEAU_DIRECTION => $this->direction?->nom ?? 'Direction',
            self::NIVEAU_SOUS_DIRECTION => $this->sousDirection?->nom ?? 'Sous-direction',
            self::NIVEAU_SERVICE => $this->service?->nom ?? 'Service',
            default => 'Direction Générale',
        };
    }

    /** Mandats en vigueur a une date (aujourd'hui par defaut). */
    public function scopeActifA(Builder $query, ?Carbon $date = null): Builder
    {
        $date ??= today();

        return $query->whereDate('date_debut', '<=', $date)
            ->where(fn ($w) => $w->whereNull('date_fin')->orWhereDate('date_fin', '>=', $date));
    }

    /** L'interim passe avant le titulaire, puis le mandat le plus recent. */
    public function scopePrioritaire(Builder $query): Builder
    {
        return $query->orderByRaw("CASE WHEN type = 'interim' THEN 0 ELSE 1 END")
            ->orderByDesc('date_debut')
            ->orderByDesc('id'); // même jour : la nomination la plus récente l'emporte
    }

    /** Nom de la colonne etrangere correspondant a un niveau (null pour la Direction Generale). */
    public static function cleUnite(string $niveau): ?string
    {
        return match ($niveau) {
            self::NIVEAU_DIRECTION => 'direction_id',
            self::NIVEAU_SOUS_DIRECTION => 'sous_direction_id',
            self::NIVEAU_SERVICE => 'service_id',
            default => null,
        };
    }

    /**
     * Responsable de la Direction Generale a une date : premier mandat actif dont l'agent est actif.
     * (La DG n'a pas de ligne dans la base : c'est une unite "virtuelle".)
     */
    public static function directeurGeneral(?Carbon $date = null): ?Personnel
    {
        return static::where('niveau', self::NIVEAU_DG)
            ->actifA($date)->prioritaire()->with('personnel')->get()
            ->pluck('personnel')
            ->first(fn ($p) => $p && $p->actif);
    }

    /** Mandat en poste d'une unité à une date : celui que retient le tableau (intérim d'abord, puis le plus récent). */
    public static function enPoste(string $niveau, ?int $uniteId, ?Carbon $date = null): ?self
    {
        $cle = self::cleUnite($niveau);

        return static::where('niveau', $niveau)
            ->when($cle, fn ($q) => $q->where($cle, $uniteId))
            ->actifA($date)->prioritaire()->first();
    }

    /**
     * Nomme un responsable. Garde l'historique : rien n'est efface.
     *  - ferme le mandat en cours du meme type la veille de la prise de fonction ;
     *  - un nouveau TITULAIRE met aussi fin a l'interim en cours (l'interim couvrait la vacance du poste).
     *
     * Exemple : MandatResponsable::nommer('service', $service->id, $agent->id, '2026-11-01', 'titulaire', 'Arrete n°12', auth()->id());
     */
    public static function nommer(
        string $niveau,
        ?int $uniteId,
        int $personnelId,
        $dateDebut = null,
        string $type = self::TITULAIRE,
        ?string $referenceActe = null,
        ?int $parUserId = null,
        $dateFin = null,
    ): self {
        $dateDebut = Carbon::parse($dateDebut ?? today())->startOfDay();
        $dateFin = $dateFin ? Carbon::parse($dateFin)->startOfDay() : null;
        $cle = self::cleUnite($niveau);

        return DB::transaction(function () use ($niveau, $uniteId, $personnelId, $dateDebut, $dateFin, $type, $referenceActe, $parUserId, $cle) {
            $typesAFermer = $type === self::TITULAIRE ? [self::TITULAIRE, self::INTERIM] : [$type];

            $aFermer = static::where('niveau', $niveau)
                ->when($cle, fn ($q) => $q->where($cle, $uniteId))
                ->whereIn('type', $typesAFermer)
                ->where(fn ($q) => $q->whereNull('date_fin')->orWhereDate('date_fin', '>=', $dateDebut))
                ->get();

            foreach ($aFermer as $mandat) {
                $fin = $dateDebut->copy()->subDay();
                if ($fin->lt($mandat->date_debut)) {
                    $fin = $mandat->date_debut->copy(); // mandat commence apres : on le ferme le jour meme
                }

                $mandat->update([
                    'date_fin' => $fin,
                    'motif_fin' => ($mandat->type === self::INTERIM && $type === self::TITULAIRE) ? "fin d'intérim" : 'remplacement',
                ]);
            }

            $donnees = [
                'niveau' => $niveau,
                'personnel_id' => $personnelId,
                'type' => $type,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'reference_acte' => $referenceActe,
                'cree_par' => $parUserId,
            ];
            if ($cle) {
                $donnees[$cle] = $uniteId; // direction_id, sous_direction_id ou service_id
            }

            return static::create($donnees);
        });
    }
}
