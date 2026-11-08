<?php

namespace App\Support;

use App\Models\StatutExecution;
use Illuminate\Support\Collection;

/**
 * Prépare les données des vues Matrice, Calendrier et Gantt des tâches.
 * Les pages Blade ne font qu'afficher : elles reçoivent des tableaux prêts à l'emploi.
 *
 * Tons (couleurs définies dans public/assets/css/taches-vues.css) :
 *   retard (rouge) > terminee (vert) / neutre (annulée) > encours (bleu) > attente (orange)
 */
class VuesTachesDonnees
{
    /** Teintes des axes (dans l'ordre), pour la matrice et le Gantt. */
    private const TEINTES = ['encours', 'attente', 'terminee', 'avenir', 'teal'];

    /** Ton du statut seul (sans tenir compte du retard) : terminee, neutre (annulée), encours, attente. */
    public static function tonStatut($tache): string
    {
        return match ($tache->statutExecution?->code) {
            StatutExecution::TERMINEE => 'terminee',
            StatutExecution::ANNULEE => 'neutre',
            StatutExecution::EN_COURS => 'encours',
            default => 'attente',
        };
    }

    /** Ton d'une tâche : le retard prime, sauf pour les tâches closes (terminées ou annulées). */
    public static function ton($tache): string
    {
        $statut = self::tonStatut($tache);

        if (in_array($statut, ['terminee', 'neutre'], true)) {
            return $statut;
        }

        return $tache->estEnRetard() ? 'retard' : $statut;
    }

    /** Agents d'une tâche pour un rôle donné (responsable, pilote, contributeur). */
    public static function agents($tache, $roleId): Collection
    {
        return $tache->affectations
            ->where('role_affectation_id', $roleId)
            ->pluck('personnel')
            ->filter()
            ->unique('id')
            ->values();
    }

    /** Compteurs affichés en tête des trois pages. Chaque tâche est comptée dans UNE catégorie. */
    public static function resume(Collection $taches): array
    {
        $tons = $taches->countBy(fn ($t) => self::ton($t));
        $n = $taches->count();

        return [
            'total' => $n,
            'tons' => [
                'retard' => $tons['retard'] ?? 0,
                'encours' => $tons['encours'] ?? 0,
                'attente' => $tons['attente'] ?? 0,
                'terminee' => $tons['terminee'] ?? 0,
                'neutre' => $tons['neutre'] ?? 0,
            ],
            'avancement' => $n ? (int) round($taches->avg('avancement_pourcentage')) : 0,
        ];
    }

    /* ------------------------------------------------------------------
     | MATRICE : Axe > Action clé > Action mesurable > Tâches (+ statistiques)
     ------------------------------------------------------------------ */

    public static function matrice(Collection $hierarchie): Collection
    {
        return $hierarchie->values()->map(function ($noeudAxe, $i) {
            $actionsCles = $noeudAxe['actionsCles']->map(function ($ac) {
                $actionsMesurables = $ac['actionsMesurables']->map(fn ($am) => $am + self::stats($am['taches']));
                $toutes = $ac['actionsMesurables']->flatMap(fn ($am) => $am['taches']);

                return [
                    'actionCle' => $ac['actionCle'],
                    'actionsMesurables' => $actionsMesurables,
                    'nbTaches' => $ac['nbTaches'],
                ] + self::stats($toutes);
            });

            $toutes = $noeudAxe['actionsCles']->flatMap(
                fn ($ac) => $ac['actionsMesurables']->flatMap(fn ($am) => $am['taches'])
            );

            return [
                'axe' => $noeudAxe['axe'],
                'actionsCles' => $actionsCles,
                'nbTaches' => $noeudAxe['nbTaches'],
                'teinte' => self::TEINTES[$i % count(self::TEINTES)],
            ] + self::stats($toutes);
        });
    }

    private static function stats(Collection $taches): array
    {
        $n = $taches->count();

        return [
            'avancement' => $n ? (int) round($taches->avg('avancement_pourcentage')) : 0,
            'terminees' => $taches->filter(fn ($t) => self::ton($t) === 'terminee')->count(),
            'retards' => $taches->filter(fn ($t) => self::ton($t) === 'retard')->count(),
        ];
    }

    /* ------------------------------------------------------------------
     | CALENDRIER : événements FullCalendar + listes du panneau latéral
     ------------------------------------------------------------------ */

    public static function calendrier(Collection $taches, $roleId): array
    {
        $evenements = $taches
            ->filter(fn ($t) => $t->date_debut || $t->date_fin)
            ->map(function ($t) use ($roleId) {
                [$debut, $fin] = self::periode($t);
                $ton = self::ton($t);

                return [
                    'id' => $t->id,
                    'title' => $t->code.' — '.$t->libelle,
                    'start' => $debut->toDateString(),
                    'end' => $fin->copy()->addDay()->toDateString(), // FullCalendar : fin exclusive
                    'allDay' => true,
                    'classNames' => ['ev', 'ev-'.$ton],
                    'extendedProps' => [
                        'code' => $t->code,
                        'libelle' => $t->libelle,
                        'ton' => $ton,
                        'statut' => $t->statutExecution?->libelle ?? '—',
                        'avancement' => (int) $t->avancement_pourcentage,
                        'sousTaches' => self::sousTaches($t),
                        'debut' => optional($t->date_debut)->format('d/m/Y'),
                        'fin' => optional($t->date_fin)->format('d/m/Y'),
                        'responsables' => self::agents($t, $roleId)->map(fn ($p) => $p->nomComplet())->implode(', '),
                        'url' => route('feuille_de_route.tache.details', $t->id),
                    ],
                ];
            })
            ->values()
            ->all();

        $ouvertes = $taches->filter(
            fn ($t) => $t->date_fin && ! in_array(self::ton($t), ['terminee', 'neutre'], true)
        );

        $prochaines = $ouvertes
            ->filter(fn ($t) => self::ton($t) !== 'retard'
                && $t->date_fin->copy()->startOfDay()->between(today(), today()->addDays(14)->endOfDay()))
            ->sortBy(fn ($t) => $t->date_fin->timestamp)
            ->take(8)
            ->map(fn ($t) => self::ligne($t))
            ->values()
            ->all();

        $retards = $ouvertes
            ->filter(fn ($t) => self::ton($t) === 'retard')
            ->sortBy(fn ($t) => $t->date_fin->timestamp)
            ->take(6)
            ->map(fn ($t) => self::ligne($t))
            ->values()
            ->all();

        return compact('evenements', 'prochaines', 'retards');
    }

    private static function ligne($t): array
    {
        return [
            'code' => $t->code,
            'libelle' => $t->libelle,
            'jour' => $t->date_fin->format('d'),
            'mois' => $t->date_fin->translatedFormat('M'),
            'jours' => (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false), // > 0 : à venir
            'ton' => self::ton($t),
            'url' => route('feuille_de_route.tache.details', $t->id),
        ];
    }

    /* ------------------------------------------------------------------
     | GANTT : tâches datées, groupées par axe
     ------------------------------------------------------------------ */

    public static function gantt(Collection $taches, $roleId): array
    {
        $avecDates = $taches->filter(fn ($t) => $t->date_debut || $t->date_fin);

        $idAxe = fn ($t) => $t->actionMesurable?->actionCle?->axe_id ?? 0;
        $idAc = fn ($t) => $t->actionMesurable?->action_cle_id ?? 0;

        // Les chiffres portent sur TOUTES les tâches (datées ou non) : ils concordent avec la liste des tâches
        $toutesParAxe = $taches->groupBy($idAxe);
        $toutesParAc = $taches->groupBy($idAc);

        $groupes = $avecDates
            ->groupBy($idAxe)
            ->map(function ($liste, $axeId) use ($roleId, $idAc, $toutesParAxe, $toutesParAc) {
                $axe = $liste->first()->actionMesurable?->actionCle?->axe;

                return [
                    'ordre' => $axe?->ordre ?? PHP_INT_MAX,
                    'id' => $axe?->id ?? 0,
                    'code' => $axe?->code ?? '—',
                    'libelle' => $axe?->libelle ?? 'Sans axe',
                    'exercice' => $axe?->exercice?->annee,
                    'stats' => self::statsGantt($toutesParAxe[$axeId]),
                    'actionsCles' => $liste
                        ->groupBy($idAc)
                        ->map(function ($l, $acId) use ($toutesParAc) {
                            $ac = $l->first()->actionMesurable?->actionCle;

                            return [
                                'id' => $acId,
                                'ordre' => $ac?->ordre ?? PHP_INT_MAX,
                                'code' => $ac?->code ?? '—',
                                'libelle' => $ac?->libelle ?? 'Sans action clé',
                                'stats' => self::statsGantt($toutesParAc[$acId]),
                            ];
                        })
                        ->sortBy('ordre')
                        ->values()
                        ->all(),
                    'taches' => $liste
                        ->map(fn ($t) => self::tacheGantt($t, $roleId))
                        ->sortBy(fn ($x) => $x['debut'].$x['code'])
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('ordre')
            ->values()
            ->map(fn ($g, $i) => $g + ['teinte' => self::TEINTES[$i % count(self::TEINTES)]])
            ->all();

        $plusieursExercices = collect($groupes)->pluck('exercice')->filter()->unique()->count() > 1;

        return [
            'groupes' => $groupes,
            'plusieursExercices' => $plusieursExercices,
            'sansDates' => $taches->count() - $avecDates->count(),
            'aujourdhui' => today()->toDateString(),
        ];
    }

    /** Chiffres d'un axe ou d'une action clé : nombre, avancement moyen, retards, retards non justifiés. */
    private static function statsGantt(Collection $taches): array
    {
        return self::stats($taches) + [
            'nb' => $taches->count(),
            'nonJustifies' => $taches->filter(fn ($t) => $t->etatJustification() === 'non_justifie')->count(),
        ];
    }

    private static function tacheGantt($t, $roleId): array
    {
        [$debut, $fin] = self::periode($t);

        return [
            'id' => $t->id,
            'acId' => $t->actionMesurable?->action_cle_id ?? 0,
            'code' => $t->code,
            'libelle' => $t->libelle,
            'chemin' => trim(($t->actionMesurable?->actionCle?->code ?? '').' / '.($t->actionMesurable?->code ?? ''), ' /'),
            'debut' => $debut->toDateString(),
            'fin' => $fin->toDateString(),
            'jalon' => ! ($t->date_debut && $t->date_fin), // une seule date : jalon
            'avancement' => (int) $t->avancement_pourcentage,
            'sousTaches' => self::sousTaches($t),
            'statut' => $t->statutExecution?->libelle ?? '—',
            'ton' => self::ton($t),
            'debutTxt' => optional($t->date_debut)->format('d/m/Y'),
            'finTxt' => optional($t->date_fin)->format('d/m/Y'),
            'responsables' => self::agents($t, $roleId)->map(fn ($p) => $p->nomComplet())->implode(', '),
            'deps' => $t->relationLoaded('dependances') ? $t->dependances->pluck('id')->all() : [],
            'url' => route('feuille_de_route.tache.details', $t->id),
        ];
    }

    /** "3/5" (sous-tâches faites / total), null si la tâche n'a pas de sous-tâche. Nécessite ->avecCompteSousTaches(). */
    private static function sousTaches($t): ?string
    {
        return ($t->sous_taches_count ?? 0) > 0 ? ($t->sous_taches_cochees_count ?? 0).'/'.$t->sous_taches_count : null;
    }

    /** [début, fin] d'une tâche : si une seule date est connue, elle sert aux deux ; ordre corrigé si inversé. */
    private static function periode($t): array
    {
        $debut = $t->date_debut ?? $t->date_fin;
        $fin = $t->date_fin ?? $t->date_debut;

        return $fin->lt($debut) ? [$fin, $debut] : [$debut, $fin];
    }
}
