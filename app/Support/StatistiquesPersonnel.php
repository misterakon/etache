<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;

/** Chiffres du tableau de bord du personnel : effectifs, répartitions, pyramide des âges, retraites prévues, complétude. */
class StatistiquesPersonnel
{
    public const TRANCHES = ['Moins de 25 ans' => [0, 24], '25 – 34 ans' => [25, 34], '35 – 44 ans' => [35, 44], '45 – 54 ans' => [45, 54], '55 ans et plus' => [55, 200]];

    public static function calculer(): array
    {
        $tous = Personnel::with([
            'service.sousDirection.direction', 'service.directionDirecte', 'typeAgent', 'genre', 'user:id,personnel_id,actif',
            'mandats' => fn ($q) => $q->actifA()->with(['direction', 'sousDirection.direction', 'service.sousDirection.direction']),
        ])->get();

        $archives = $tous->filter->estArchive();
        $courants = $tous->reject->estArchive();
        $actifs = $courants->where('actif', true)->values();

        $parDirection = $actifs->groupBy(fn ($a) => self::direction($a))->map->count()->sortDesc();
        $parType = $actifs->groupBy(fn ($a) => $a->typeAgent?->libelle ?? 'Non renseigné')->map->count()->sortDesc();
        $parGenre = $actifs->groupBy(fn ($a) => $a->genre?->libelle ?? 'Non renseigné')->map->count()->sortDesc();

        // Pyramide des âges (hommes / femmes) par tranche
        $pyramide = [];
        foreach (self::TRANCHES as $libelle => [$min, $max]) {
            $pyramide[$libelle] = ['h' => 0, 'f' => 0, 'n' => 0];
        }
        $sansDate = 0;
        foreach ($actifs as $a) {
            if (! $a->date_naissance) {
                $sansDate++;

                continue;
            }
            $age = $a->date_naissance->age;
            foreach (self::TRANCHES as $libelle => [$min, $max]) {
                if ($age >= $min && $age <= $max) {
                    $g = mb_strtolower((string) $a->genre?->libelle);
                    $pyramide[$libelle][str_starts_with($g, 'f') ? 'f' : (str_starts_with($g, 'm') ? 'h' : 'n')]++;
                    break;
                }
            }
        }

        // Retraites prévues : date renseignée, sinon naissance + âge de retraite des Réglages
        $ageRetraite = (int) Reglages::get('personnel.age_retraite');
        $retraites = $actifs->map(function ($a) use ($ageRetraite) {
            $date = $a->date_depart_retraite ?? $a->date_naissance?->copy()->addYears($ageRetraite);

            return $date ? ['agent' => $a, 'date' => $date, 'estimee' => $a->date_depart_retraite === null] : null;
        })->filter()->filter(fn ($r) => $r['date']->gte(today()) && $r['date']->lte(today()->addYears(3)))->sortBy(fn ($r) => $r['date']->timestamp)->values();

        $anciennete = $actifs->filter(fn ($a) => $a->date_prise_service)->map(fn ($a) => $a->date_prise_service->diffInMonths(today()) / 12);
        $completude = $courants->map(fn ($a) => CompletudeFiche::analyser($a)['score']);

        return [
            'effectifs' => [
                'total' => $courants->count(), 'actifs' => $actifs->count(), 'inactifs' => $courants->count() - $actifs->count(), 'archives' => $archives->count(),
                'avec_compte' => $actifs->filter(fn ($a) => $a->user !== null)->count(), 'sans_compte' => $actifs->filter(fn ($a) => $a->user === null)->count(),
            ],
            'par_direction' => $parDirection, 'par_type' => $parType, 'par_genre' => $parGenre,
            'pyramide' => $pyramide, 'sans_date_naissance' => $sansDate,
            'age_moyen' => $actifs->filter(fn ($a) => $a->date_naissance)->avg(fn ($a) => $a->date_naissance->age),
            'anciennete_moyenne' => $anciennete->isNotEmpty() ? $anciennete->avg() : null,
            'retraites' => $retraites, 'retraites_12_mois' => $retraites->filter(fn ($r) => $r['date']->lte(today()->addYear()))->count(),
            'completude_moyenne' => $completude->isNotEmpty() ? (int) round($completude->avg()) : 0,
            'fiches_incompletes' => $courants->filter(fn ($a) => CompletudeFiche::analyser($a)['essentiels'] !== [])->count(),
        ];
    }

    public static function direction(Personnel $a): string
    {
        if ($direction = $a->service?->direction()) {
            return $direction->nom;
        }
        foreach ($a->mandats as $m) {
            $nom = match ($m->niveau) {
                MandatResponsable::NIVEAU_DIRECTION => $m->direction?->nom,
                MandatResponsable::NIVEAU_SOUS_DIRECTION => $m->sousDirection?->direction?->nom,
                MandatResponsable::NIVEAU_SERVICE => $m->service?->direction()?->nom,
                default => null,
            };
            if ($nom) {
                return $nom;
            }
        }

        return $a->fonction_id || $a->mandats->contains('niveau', MandatResponsable::NIVEAU_DG) ? 'Direction Générale' : 'Non rattaché';
    }
}
