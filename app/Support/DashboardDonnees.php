<?php

namespace App\Support;

use App\Models\TacheJustification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Prépare les données du tableau de bord e-Tache :
 * compteurs, tâches prioritaires, diligences (à venir ce mois puis retards) et performance du personnel.
 *
 * Reçoit les tâches DÉJÀ filtrées sur l'exercice affiché (voir MainController::dashboard()).
 * Chaque tâche est comptée dans UNE catégorie (VuesTachesDonnees::ton) :
 * retard > terminée / annulée > en cours > en attente.
 */
class DashboardDonnees
{
    private const NB_PRIORITAIRES = 8;

    private const NB_PERSONNEL = 10;

    /**
     * @param  Collection  $taches  avec statutExecution, priorite, actionMesurable.actionCle.axe, affectations.personnel
     */
    public static function depuis(Collection $taches, $roleResponsableId = null, bool $vuePersonnelle = false): array
    {
        $resume = VuesTachesDonnees::resume($taches);
        $tons = $resume['tons'];
        $total = $resume['total'];
        $pct = fn (int $n) => $total ? (int) round($n / $total * 100) : 0;

        $diligences = self::diligences($taches, $roleResponsableId);

        return [
            'stats' => [
                'total' => $total,
                'attente' => $tons['attente'],
                'terminees' => $tons['terminee'],
                'encours' => $tons['encours'],
                'retards' => $tons['retard'],
                'diligences' => $diligences['nb'],
                'pctAttente' => $pct($tons['attente']),
                'pctTerminees' => $pct($tons['terminee']),
                'pctEncours' => $pct($tons['encours']),
                'pctRetards' => $pct($tons['retard']),
                'avancement' => $resume['avancement'],
                'retardsNonJustifies' => self::retardsNonJustifies($taches),
            ],
            'prioritaires' => self::prioritaires($taches, $roleResponsableId),
            'diligences' => $diligences['lignes'],
            'performance' => self::performance($taches),
            'budget' => self::budget($taches),
            'justifications' => self::justifications($taches, $vuePersonnelle),
        ];
    }

    /* ------------------------------------------------------------------
     | Tâches prioritaires : ouvertes, par priorité, puis retard, puis échéance
     ------------------------------------------------------------------ */
    private static function prioritaires(Collection $taches, $roleId): array
    {
        $rang = ['retard' => 0, 'attente' => 1, 'avenir' => 2, 'neutre' => 3];

        return $taches
            ->filter(fn ($t) => ! in_array(VuesTachesDonnees::ton($t), ['terminee', 'neutre'], true))
            ->sortBy(function ($t) use ($rang) {
                $retard = VuesTachesDonnees::ton($t) === 'retard' ? 0 : 1;

                return (($rang[self::tonPriorite($t->priorite?->libelle)] * 10) + $retard) * 10_000_000_000
                    + ($t->date_fin?->timestamp ?? 9_999_999_999);
            })
            ->take(self::NB_PRIORITAIRES)
            ->values()
            ->map(fn ($t) => [
                'tache' => $t,
                'ton' => VuesTachesDonnees::ton($t),
                'priorite' => $t->priorite?->libelle,
                'tonPriorite' => self::tonPriorite($t->priorite?->libelle),
                'chemin' => collect([
                    $t->actionMesurable?->actionCle?->axe?->code,
                    $t->actionMesurable?->actionCle?->code,
                    $t->actionMesurable?->code,
                ])->filter()->implode(' / '),
                'agents' => self::agents($t, $roleId),
                'jours' => $t->date_fin ? (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false) : null,
            ])
            ->all();
    }

    /* ------------------------------------------------------------------
     | Diligences : tâches à venir ce mois (aujourd'hui -> fin du mois), puis tâches en retard à la suite
     ------------------------------------------------------------------ */
    private static function diligences(Collection $taches, $roleId): array
    {
        $finMois = today()->endOfMonth();

        $ouvertes = $taches->filter(
            fn ($t) => $t->date_fin && ! in_array(VuesTachesDonnees::ton($t), ['terminee', 'neutre'], true)
        );

        $avenir = $ouvertes
            ->filter(fn ($t) => VuesTachesDonnees::ton($t) !== 'retard'
                && $t->date_fin->copy()->startOfDay()->between(today(), $finMois))
            ->sortBy(fn ($t) => $t->date_fin->timestamp)
            ->values();

        // Les retards les plus anciens d'abord
        $retards = $ouvertes
            ->filter(fn ($t) => VuesTachesDonnees::ton($t) === 'retard')
            ->sortBy(fn ($t) => $t->date_fin->timestamp)
            ->values();

        $toutes = $avenir->concat($retards);

        return [
            'nb' => $toutes->count(),
            'lignes' => $toutes
                ->map(fn ($t) => [
                    'tache' => $t,
                    'ton' => VuesTachesDonnees::ton($t),
                    'agents' => self::agents($t, $roleId), // TOUS les acteurs, responsables d'abord
                    'jours' => (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false), // < 0 : retard
                    'chemin' => collect([
                        $t->actionMesurable?->actionCle?->code,
                        $t->actionMesurable?->code,
                    ])->filter()->implode(' / '),
                ])
                ->all(),
        ];
    }

    /* ------------------------------------------------------------------
     | Performance du personnel : tâches affectées / terminées / taux (annulées exclues)
     ------------------------------------------------------------------ */
    private static function performance(Collection $taches): array
    {
        $agents = [];

        foreach ($taches as $t) {
            $ton = VuesTachesDonnees::ton($t);
            if ($ton === 'neutre') {
                continue;
            }

            foreach ($t->affectations->pluck('personnel')->filter()->unique('id') as $p) {
                $agents[$p->id] ??= ['personnel' => $p, 'affectees' => 0, 'terminees' => 0];
                $agents[$p->id]['affectees']++;
                if ($ton === 'terminee') {
                    $agents[$p->id]['terminees']++;
                }
            }
        }

        return collect($agents)
            ->map(fn ($a) => $a + ['taux' => $a['affectees'] ? (int) round($a['terminees'] / $a['affectees'] * 100) : 0])
            ->sortBy([['affectees', 'desc'], ['taux', 'desc']])
            ->take(self::NB_PERSONNEL)
            ->values()
            ->all();
    }

    /* ------------------------------------------------------------------
     | Budget : répartition par catégorie (mêmes catégories exclusives que les compteurs :
     | retard > terminée / annulée > en cours > en attente) + géométrie des arcs de l'anneau
     ------------------------------------------------------------------ */
    private static function budget(Collection $taches): array
    {
        $parTon = ['terminee' => 0.0, 'encours' => 0.0, 'attente' => 0.0, 'retard' => 0.0, 'neutre' => 0.0];
        foreach ($taches as $t) {
            $ton = VuesTachesDonnees::ton($t);
            $parTon[$ton] = ($parTon[$ton] ?? 0.0) + (float) $t->budget_estime;
        }

        $total = array_sum($parTon);

        // [libellé, couleur, montant, aide affichée au survol]
        $segments = [
            ['Terminées', 'terminee', $parTon['terminee'], ''],
            ['En cours', 'encours', $parTon['encours'], ''],
            ['En attente', 'attente', $parTon['attente'], 'Non démarrées, bloquées ou suspendues, dans les délais'],
            ['En retard', 'retard', $parTon['retard'], 'Échéance dépassée, tâche non terminée'],
        ];
        if ($parTon['neutre'] > 0.5) {
            $segments[] = ['Annulées', 'neutre', $parTon['neutre'], ''];
        }

        // Arcs de l'anneau : [couleur, longueur, début] sur un total de 100 ; petit espace entre les arcs
        $nbArcs = count(array_filter($segments, fn ($s) => $s[2] > 0));
        $ecart = $nbArcs > 1 ? 0.8 : 0;
        $arcs = [];
        $debut = 0.0;
        foreach ($segments as [, $ton, $montant]) {
            $part = $total > 0 ? $montant / $total * 100 : 0;
            if ($part > 0) {
                $arcs[] = [$ton, round(max($part - $ecart, 0.1), 2), round($debut, 2)];
            }
            $debut += $part;
        }

        return [
            'total' => $total,
            'termine' => $parTon['terminee'],
            'retard' => $parTon['retard'],
            'pctRealise' => $total > 0 ? (int) round($parTon['terminee'] / $total * 100) : 0,
            'pctRetard' => $total > 0 ? (int) round($parTon['retard'] / $total * 100) : 0,
            'segments' => $segments,
            'arcs' => $arcs,
        ];
    }

    /* ------------------------------------------------------------------
     | Retards non justifiés : en retard, sans demande en attente ni justification approuvée valable
     ------------------------------------------------------------------ */
    private static function retardsNonJustifies(Collection $taches): int
    {
        $user = auth()->user();
        $retards = $taches->filter(fn ($t) => VuesTachesDonnees::ton($t) === 'retard');

        if (! $user || $retards->isEmpty() || ! Schema::hasTable('tache_justifications')) {
            return 0;
        }

        $ctx = JustificationRetard::contexte($user, $retards);

        return $retards->reject(fn ($t) => in_array($t->id, $ctx['justifiees'], true) || $ctx['enAttente']->has($t->id))->count();
    }

    /* ------------------------------------------------------------------
     | Demandes de justification / de report (table tache_justifications) : nombre par statut pour les tâches affichées.
     |  - ADMIN, DG, RD : mêmes règles de visibilité que la page des demandes ;
     |  - vue personnelle (agent) : CHIFFRES SEULEMENT, pour les demandes portant sur SES tâches
     |    (la page des demandes ne lui est pas ouverte : les tuiles ne sont pas cliquables).
     | 'visible' vaut false si le module n'est pas installé ou si l'utilisateur n'a pas le droit de voir ce bloc.
     ------------------------------------------------------------------ */
    private static function justifications(Collection $taches, bool $vuePersonnelle = false): array
    {
        $statuts = ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
        $user = auth()->user();

        $visible = Schema::hasTable('tache_justifications')
            && $user
            && ($vuePersonnelle || $user->hasRole('ADMIN|DG|RD'));

        if ($visible && $taches->isNotEmpty()) {
            $ids = $taches->pluck('id');

            if ($vuePersonnelle) {
                // Les tâches sont déjà limitées à celles de l'agent : on compte toutes les demandes qui les concernent
                $comptes = TacheJustification::whereIn('tache_id', $ids)
                    ->selectRaw('statut, COUNT(*) AS nb')
                    ->groupBy('statut')
                    ->pluck('nb', 'statut');

                foreach (array_keys($statuts) as $cle) {
                    $statuts[$cle] = (int) ($comptes[$cle] ?? 0);
                }
            } else {
                // En attente : celles que CET utilisateur doit décider (comme la liste "À valider" de la page)
                $statuts['en_attente'] = TacheJustification::enAttente()->aDecider($user)->whereIn('tache_id', $ids)->count();

                // Approuvées, refusées, annulées : celles qu'il a le droit de voir
                $comptes = TacheJustification::visiblePar($user)
                    ->whereIn('tache_id', $ids)
                    ->where('statut', '!=', TacheJustification::EN_ATTENTE)
                    ->selectRaw('statut, COUNT(*) AS nb')
                    ->groupBy('statut')
                    ->pluck('nb', 'statut');

                foreach (['approuvee', 'refusee', 'annulee'] as $cle) {
                    $statuts[$cle] = (int) ($comptes[$cle] ?? 0);
                }
            }
        }

        return ['visible' => $visible, 'total' => array_sum($statuts), 'statuts' => $statuts];
    }

    /* ------------------------------------------------------------------ */

    /** Acteurs d'une tâche (uniques), les responsables en premier. */
    private static function agents($tache, $roleResponsableId = null): Collection
    {
        return $tache->affectations
            ->filter(fn ($a) => $a->personnel)
            ->sortBy(fn ($a) => $roleResponsableId && $a->role_affectation_id == $roleResponsableId ? 0 : 1)
            ->pluck('personnel')
            ->unique('id')
            ->values();
    }

    /** Ton d'une priorité d'après son libellé : haute (rouge), moyenne (orange), autre (violet), non définie (gris). */
    private static function tonPriorite(?string $libelle): string
    {
        $l = mb_strtolower((string) $libelle);

        return match (true) {
            $l === '' => 'neutre',
            str_contains($l, 'haut'), str_contains($l, 'urgent'), str_contains($l, 'critique'), str_contains($l, 'élev') => 'retard',
            str_contains($l, 'moy') => 'attente',
            default => 'avenir',
        };
    }
}
