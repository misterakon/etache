<?php

namespace App\Support;

use App\Models\Axe;
use App\Models\Diligence;
use App\Models\Direction;
use App\Models\Exercice;
use App\Models\RoleAffectation;
use App\Models\StatutExecution;
use App\Models\SynthesePilotage;
use App\Models\Tache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Données de la page « Pilotage » (cockpit du DG) : contexte commun (exercice, période, périmètre), indicateurs du bandeau
 * et contenu de chaque onglet. Réutilise AnalysesPilotage, ChargeTravail et les exports existants : aucune règle nouvelle.
 */
class Cockpit
{
    /** @var array<string, array{0: string, 1: string}> clé de l'onglet => [libellé, icône Font Awesome] */
    public const ONGLETS = [
        'ensemble' => ['Vue d’ensemble', 'fa-gauge-high'],
        'taches' => ['Tâches', 'fa-list-check'],
        'courrier' => ['Courrier et diligences', 'fa-envelope-open-text'],
        'structure' => ['Directions et axes', 'fa-sitemap'],
        'agents' => ['Agents et charge', 'fa-users'],
        'evolution' => ['Évolution', 'fa-chart-line'],
        'synthese' => ['Synthèse rédigée', 'fa-feather-pointed'],
        'exports' => ['Exports et impressions', 'fa-download'],
    ];

    /** Filtres de l'onglet Tâches : clé => [libellé, ton]. */
    public const FILTRES_TACHES = [
        'retards' => ['En retard', 'rouge'],
        'non_justifies' => ['Non justifiés', 'ambre'],
        'proches' => ['Échéances proches', 'orange'],
        'en_cours' => ['En cours', 'bleu'],
        'attente' => ['En attente', 'gris'],
        'sans_responsable' => ['Sans responsable', 'sarcelle'],
        'terminees' => ['Terminées', 'vert'],
        'toutes' => ['Toutes', 'neutre'],
    ];

    /** Correspondance filtre de l'écran => type d'export des tâches (ExportTaches::TYPES). */
    public const TYPE_EXPORT = [
        'retards' => 'en_retard', 'non_justifies' => 'non_justifies', 'proches' => 'a_venir', 'en_cours' => 'en_cours',
        'sans_responsable' => 'sans_responsable', 'terminees' => 'terminees', 'toutes' => 'toutes',
    ];

    // ------------------------------------------------------------------ contexte
    private static function portee(?string $brut, Exercice $e, ?array $dirs): array
    {
        if ($brut && preg_match('/^(axe|direction):(\d+)$/', $brut, $m)) {
            $id = (int) $m[2];
            if ($m[1] === 'axe' && Axe::where('exercice_id', $e->id)->whereKey($id)->exists()) {
                return ['axe', $id];
            }
            if ($m[1] === 'direction' && ($dirs === null || in_array($id, $dirs, true)) && Direction::whereKey($id)->exists()) {
                return ['direction', $id];
            }
        }

        return ['global', null];
    }

    /** Choix de l'utilisateur (exercice, période, périmètre) : toujours borné à son périmètre de direction. */
    public static function contexte(Request $request): array
    {
        $dirs = PerimetrePilotage::directions($request->user());
        $exercices = Exercice::orderByDesc('annee')->get();
        $exercice = $exercices->firstWhere('id', $request->integer('exercice_id')) ?? Exercice::parDefaut() ?? $exercices->first();
        if (! $exercice) {
            return ['exercice' => null, 'exercices' => $exercices, 'dirs' => $dirs];
        }

        $type = array_key_exists((string) $request->input('type'), AnalysesPilotage::TYPES) ? (string) $request->input('type') : 'annee';
        $periode = AnalysesPilotage::periode($type, $request->filled('valeur') ? $request->integer('valeur') : null, $exercice);
        [$porteeType, $porteeId] = self::portee($request->input('portee'), $exercice, $dirs);

        return [
            'exercice' => $exercice, 'exercices' => $exercices, 'dirs' => $dirs, 'periode' => $periode,
            'porteeType' => $porteeType, 'porteeId' => $porteeId, 'porteeBrut' => $porteeType === 'global' ? 'global' : $porteeType.':'.$porteeId,
        ];
    }

    public static function parametres(array $c): array
    {
        return ['exercice_id' => $c['exercice']->id, 'type' => $c['periode']['type'], 'valeur' => $c['periode']['valeur'], 'portee' => $c['porteeBrut']];
    }

    public static function analyse(array $c): array
    {
        return AnalysesPilotage::calculer($c['exercice'], $c['periode'], $c['porteeType'], $c['porteeId'], $c['dirs']);
    }

    /** Tâches du périmètre choisi (avec leurs relations d'affichage). */
    public static function taches(array $c): Builder
    {
        $q = Tache::with(['statutExecution', 'priorite', 'affectations.personnel', 'actionMesurable.actionCle.direction', 'actionMesurable.actionCle.axe'])
            ->dansExercice($c['exercice']->id);

        if ($c['porteeType'] === 'axe') {
            $q->parAxe((int) $c['porteeId']);
        } elseif ($c['porteeType'] === 'direction') {
            $q->parDirection([(int) $c['porteeId']]);
        } elseif ($c['dirs'] !== null) {
            $q->parDirection($c['dirs'] ?: [0]);
        }

        return $q;
    }

    // ------------------------------------------------------------------ bandeau
    /** @return array<string, mixed> chiffres du bandeau (identiques pour tous les onglets). */
    public static function kpis(array $a): array
    {
        $s = $a['situation'];
        $c = $a['courrier'];

        return [
            'taches' => $s['total'],
            'non_demarrees' => $s['non_demarrees'],
            'avancement' => (float) $s['avancement'],
            'taux' => (float) $s['taux'],
            'retards' => $s['en_retard'],
            'non_justifies' => $s['non_justifies'],
            'echeances' => (int) array_sum(array_slice(array_column($a['semaines'], 'valeur'), 0, 4)),
            'diligences_retard' => $c['diligences_en_retard'] ?? null,
            'diligences_ouvertes' => $c['diligences_ouvertes'] ?? null,
        ];
    }

    // ------------------------------------------------------------------ vue d'ensemble
    public static function ensemble(array $c, array $a, $user): array
    {
        $taches = self::taches($c)->get();
        $roleResp = RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->value('id');

        $parTon = ['terminee' => 0, 'encours' => 0, 'attente' => 0, 'retard' => 0, 'neutre' => 0];
        $sansResp = 0;
        $proches7 = 0;
        $anciennete = ['recent' => 0, 'moyen' => 0, 'ancien' => 0];
        foreach ($taches as $t) {
            $ton = VuesTachesDonnees::ton($t);
            $parTon[$ton] = ($parTon[$ton] ?? 0) + 1;
            if ($ton === 'retard' && $t->date_fin) {
                $j = (int) $t->date_fin->copy()->startOfDay()->diffInDays(today());
                $anciennete[$j < 30 ? 'recent' : ($j <= 90 ? 'moyen' : 'ancien')]++;
            }
            if (! in_array($ton, ['terminee', 'neutre'], true)) {
                if ($t->affectations->where('role_affectation_id', $roleResp)->isEmpty()) {
                    $sansResp++;
                }
                if ($ton !== 'retard' && $t->date_fin && $t->date_fin->between(today(), today()->addDays(7))) {
                    $proches7++;
                }
            }
        }

        $total = max(1, $taches->count());
        $repartition = [
            ['cle' => 'terminees', 'libelle' => 'Terminées', 'valeur' => $parTon['terminee'], 'couleur' => '#16A34A'],
            ['cle' => 'en_cours', 'libelle' => 'En cours', 'valeur' => $parTon['encours'], 'couleur' => '#2563EB'],
            ['cle' => 'attente', 'libelle' => 'En attente', 'valeur' => $parTon['attente'], 'couleur' => '#F59E0B'],
            ['cle' => 'retards', 'libelle' => 'En retard', 'valeur' => $parTon['retard'], 'couleur' => '#E11D48'],
        ];
        if ($parTon['neutre'] > 0) {
            $repartition[] = ['cle' => 'toutes', 'libelle' => 'Annulées', 'valeur' => $parTon['neutre'], 'couleur' => '#94A3B8'];
        }
        foreach ($repartition as &$r) {
            $r['pct'] = (int) round($r['valeur'] / $total * 100);
        }
        unset($r);

        // Écart au plan : dernier mois connu de la courbe attendu / réalisé
        $dernier = collect($a['courbe'])->filter(fn ($p) => $p['realise'] !== null)->last();
        $attendu = (float) ($dernier['attendu'] ?? 0);
        $realise = (float) ($dernier['realise'] ?? 0);
        $ecart = round($realise - $attendu, 1);

        $s = $a['situation'];
        $pctRetard = $s['total'] ? (int) round($s['en_retard'] / $s['total'] * 100) : 0;
        $ton = $pctRetard >= 30 ? 'rouge' : ($pctRetard >= 10 ? 'ambre' : 'vert');

        $nbAValider = $user ? JustificationRetard::nbAValider($user) : 0;
        $diligencesAPrendre = Diligence::where('statut', 'a_prendre_en_charge')->count();

        $aTraiter = array_values(array_filter([
            ['cle' => 'taches:non_justifies', 'libelle' => 'Retards sans justification', 'nb' => (int) $s['non_justifies'], 'ton' => 'rouge', 'icone' => 'fa-triangle-exclamation', 'aide' => 'Demander une justification ou un report'],
            ['cle' => 'url:'.route('feuille_de_route.justification.index', ['statut' => 'en_attente']), 'libelle' => 'Demandes à décider', 'nb' => $nbAValider, 'ton' => 'ambre', 'icone' => 'fa-scale-balanced', 'aide' => 'Reports et justifications en attente'],
            ['cle' => 'taches:proches', 'libelle' => 'Échéances sous 7 jours', 'nb' => $proches7, 'ton' => 'orange', 'icone' => 'fa-calendar-day', 'aide' => 'À suivre de près'],
            ['cle' => 'taches:sans_responsable', 'libelle' => 'Tâches sans responsable', 'nb' => $sansResp, 'ton' => 'sarcelle', 'icone' => 'fa-user-slash', 'aide' => 'Personne n’est chargé de les faire'],
            ['cle' => 'url:'.route('ponctuelle.index', ['portee' => 'equipe']), 'libelle' => 'Tâches ponctuelles en retard', 'nb' => self::ponctuellesEnRetard($c, $user), 'ton' => 'orange', 'icone' => 'fa-bolt', 'aide' => 'Travail hors feuille de route à relancer'],
            $c['dirs'] === null && $c['porteeType'] === 'global'
                ? ['cle' => 'courrier', 'libelle' => 'Diligences à prendre en charge', 'nb' => $diligencesAPrendre, 'ton' => 'violet', 'icone' => 'fa-inbox', 'aide' => 'Courrier imputé, pas encore pris'] : null,
        ]));

        $directions = collect($a['par_direction'])->filter(fn ($r) => $r['en_retard'] > 0)->sortByDesc('en_retard')->take(8)->values()
            ->map(fn ($r) => ['id' => $r['id'], 'libelle' => $r['libelle'], 'retards' => $r['en_retard'], 'non_justifies' => $r['non_justifies'], 'total' => $r['total']])->all();
        $axes = collect($a['par_axe'])->map(fn ($r) => ['id' => $r['id'], 'code' => $r['code'], 'libelle' => $r['libelle'], 'avancement' => (float) $r['avancement'], 'terminees' => $r['terminees'], 'total' => $r['total']])->all();
        $agents = ChargeTravail::tous()->filter(fn ($l) => $l['charge'] > 0)->take(5)
            ->map(fn ($l) => ['nom' => $l['personnel']->nomComplet(), 'charge' => $l['charge'], 'retard' => $l['retard'], 'surcharge' => $l['surcharge']])->values()->all();

        return [
            'repartition' => $repartition, 'total' => $taches->count(),
            'cap' => ['ton' => $ton, 'pct_retard' => $pctRetard, 'retards' => $s['en_retard'], 'total' => $s['total'], 'non_justifies' => $s['non_justifies'],
                'attendu' => $attendu, 'realise' => $realise, 'ecart' => $ecart],
            'courbe' => ['labels' => array_column($a['courbe'], 'mois'), 'attendu' => array_map('floatval', array_column($a['courbe'], 'attendu')), 'realise' => array_column($a['courbe'], 'realise')],
            'a_traiter' => $aTraiter, 'anciennete' => $anciennete, 'directions' => $directions, 'axes' => $axes, 'agents' => $agents,
            'courrier' => $a['courrier'],
            'ponctuelles' => $user ? PonctuellesResume::pour($user, false, $c['porteeType'] === 'direction' ? (int) $c['porteeId'] : null) : null,
        ];
    }

    /** Tâches ponctuelles en retard dans le périmètre choisi : elles comptent dans les retards, jamais dans l'avancement. */
    public static function ponctuellesEnRetard(array $c, $user): int
    {
        $q = \App\Models\TachePonctuelle::query()->enRetard()->when($user, fn ($w) => $w->visiblePar($user));
        if ($c['porteeType'] === 'direction') {
            $q->where('direction_id', $c['porteeId']);
        } elseif ($c['porteeType'] === 'axe') {
            return 0;
        }

        return $q->count();
    }

    // ------------------------------------------------------------------ tâches
    public static function listeTaches(array $c, string $filtre, $user): array
    {
        $roleResp = RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->value('id');
        $toutes = self::taches($c)->get();
        $ton = fn ($t) => VuesTachesDonnees::ton($t);
        $retards = $toutes->filter(fn ($t) => $ton($t) === 'retard');
        $ctx = $retards->isNotEmpty() ? JustificationRetard::contexte($user, $retards) : ['justifiees' => [], 'enAttente' => collect()];
        $etatJustif = fn ($t) => $ton($t) !== 'retard' ? null : (in_array($t->id, $ctx['justifiees'], true) ? 'justifie' : ($ctx['enAttente']->has($t->id) ? 'attente' : 'non_justifie'));
        $ouverte = fn ($t) => ! in_array($ton($t), ['terminee', 'neutre'], true);

        $regles = [
            'retards' => fn ($t) => $ton($t) === 'retard',
            'non_justifies' => fn ($t) => $etatJustif($t) === 'non_justifie',
            'proches' => fn ($t) => $ouverte($t) && $ton($t) !== 'retard' && $t->date_fin && $t->date_fin->between(today(), today()->addDays(30)),
            'en_cours' => fn ($t) => $t->statutExecution?->code === StatutExecution::EN_COURS && $ton($t) !== 'terminee',
            'attente' => fn ($t) => $ton($t) === 'attente',
            'sans_responsable' => fn ($t) => $ouverte($t) && $t->affectations->where('role_affectation_id', $roleResp)->isEmpty(),
            'terminees' => fn ($t) => $ton($t) === 'terminee',
            'toutes' => fn ($t) => true,
        ];

        $compteurs = array_map(fn ($regle) => $toutes->filter($regle)->count(), $regles);
        $filtre = array_key_exists($filtre, $regles) ? $filtre : 'retards';

        $lignes = $toutes->filter($regles[$filtre])
            ->sortBy(fn ($t) => $t->date_fin?->timestamp ?? PHP_INT_MAX)->take(300)->values()
            ->map(function ($t) use ($roleResp, $ton, $etatJustif) {
                $jours = $t->date_fin ? (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false) : null;

                return [
                    'id' => $t->id, 'code' => $t->code, 'libelle' => $t->libelle, 'ton' => $ton($t),
                    'direction' => $t->actionMesurable?->actionCle?->direction?->nom, 'axe' => $t->actionMesurable?->actionCle?->axe?->code,
                    'echeance' => $t->date_fin?->format('d/m/Y'), 'jours' => $jours, 'avancement' => (int) $t->avancement_pourcentage,
                    'statut' => $t->statutExecution?->libelle, 'priorite' => $t->priorite?->libelle, 'justif' => $etatJustif($t),
                    'agents' => $t->affectations->sortBy(fn ($a) => $a->role_affectation_id == $roleResp ? 0 : 1)->pluck('personnel')->filter()->unique('id')->take(3)
                        ->map(fn ($p) => ['nom' => $p->nomComplet(), 'initiales' => $p->initiales(), 'photo' => $p->photo_url])->values()->all(),
                    'url' => route('feuille_de_route.tache.details', $t->id),
                ];
            })->all();

        return ['filtre' => $filtre, 'compteurs' => $compteurs, 'lignes' => $lignes, 'total' => $compteurs[$filtre], 'tronque' => $compteurs[$filtre] > 300];
    }

    // ------------------------------------------------------------------ courrier
    public static function courrier(array $a): array
    {
        $enRetard = Diligence::ouvertes()->whereDate('echeance', '<', today())->with('responsable')->orderBy('echeance')->limit(10)->get()
            ->map(fn ($d) => [
                'id' => $d->id, 'code' => $d->code, 'responsable' => $d->responsable?->nomComplet(), 'echeance' => $d->echeance->format('d/m/Y'),
                'jours' => (int) $d->echeance->copy()->startOfDay()->diffInDays(today()), 'statut' => $d->libelleStatut(), 'url' => route('courrier.diligence.show', $d->id),
            ])->all();
        $libelles = Diligence::STATUTS;

        return [
            'c' => $a['courrier'], 'en_retard' => $enRetard,
            'graph' => $a['courrier'] ? [
                'mois' => array_values(AnalysesPilotage::MOIS_COURTS), 'recus' => array_map('intval', $a['courrier']['recus']), 'departs' => array_map('intval', $a['courrier']['departs']),
                'statuts' => collect($a['courrier']['statuts_diligences'])->map(fn ($n, $k) => ['libelle' => $libelles[$k][0] ?? $k, 'valeur' => (int) $n, 'couleur' => $libelles[$k][1] ?? '#8A94A6'])->values()->all(),
            ] : null,
        ];
    }

    // ------------------------------------------------------------------ directions et axes
    public static function structure(array $a): array
    {
        $dirs = $a['par_direction'];
        $axes = $a['par_axe'];

        return [
            'directions' => $dirs, 'axes' => $axes,
            'graph' => [
                'directions' => array_map(fn ($r) => ['libelle' => $r['libelle'], 'terminees' => (int) $r['terminees'], 'en_cours' => (int) $r['en_cours'],
                    'non_demarrees' => (int) $r['non_demarrees'], 'autres' => (int) $r['bloquees'] + (int) $r['suspendues']], $dirs),
                'axes' => array_map(fn ($r) => ['id' => $r['id'], 'libelle' => $r['code'].' · '.$r['libelle'], 'avancement' => (float) $r['avancement'], 'terminees' => (int) $r['terminees'], 'total' => (int) $r['total']], $axes),
            ],
        ];
    }

    // ------------------------------------------------------------------ agents
    public static function agents(): array
    {
        $tous = ChargeTravail::tous();
        $lignes = $tous->filter(fn ($l) => $l['charge'] > 0)->take(300)->values()->map(fn ($l) => [
            'nom' => $l['personnel']->nomComplet(), 'service' => $l['personnel']->service?->nom, 'photo' => $l['personnel']->photo_url, 'initiales' => $l['personnel']->initiales(),
            'taches' => $l['taches'], 'retard' => $l['retard'], 'diligences' => $l['diligences'], 'actions' => $l['actions'], 'charge' => $l['charge'], 'surcharge' => $l['surcharge'], 'seuil' => $l['seuil'],
            'url' => route('dgepn.personnel.fiche', $l['personnel']->id),
        ])->all();

        return [
            'lignes' => $lignes, 'mobilises' => $tous->filter(fn ($l) => $l['charge'] > 0)->count(), 'surcharges' => $tous->where('surcharge', true)->count(),
            'graph' => array_map(fn ($l) => ['libelle' => $l['nom'], 'valeur' => $l['charge'], 'surcharge' => $l['surcharge']], array_slice($lignes, 0, 10)),
        ];
    }

    // ------------------------------------------------------------------ évolution
    public static function evolution(array $c, array $a): array
    {
        $serie = ($c['dirs'] !== null && $c['porteeType'] === 'global') ? collect()
            : InstantanesPilotage::serie($c['exercice']->id, $c['porteeType'] === 'axe' ? $c['porteeId'] : null, $c['porteeType'] === 'direction' ? $c['porteeId'] : null);

        return [
            'p' => $a['periode_chiffres'], 'periode' => $a['periode'],
            'graph' => [
                'courbe' => ['labels' => array_column($a['courbe'], 'mois'), 'attendu' => array_map('floatval', array_column($a['courbe'], 'attendu')), 'realise' => array_column($a['courbe'], 'realise')],
                'semaines' => ['labels' => array_column($a['semaines'], 'libelle'), 'valeurs' => array_map('intval', array_column($a['semaines'], 'valeur'))],
                'evolution' => $serie->count() >= 2 ? [
                    'labels' => $serie->map(fn ($i) => $i->jour->format('d/m'))->all(),
                    'retards' => $serie->pluck('en_retard')->map(fn ($v) => (int) $v)->all(), 'non_justifies' => $serie->pluck('non_justifies')->map(fn ($v) => (int) $v)->all(),
                ] : null,
            ],
            'points' => $serie->count(),
        ];
    }

    // ------------------------------------------------------------------ synthèse
    private static function synthesesVisibles(array $c, $user)
    {
        return SynthesePilotage::query()->where('exercice_id', $c['exercice']->id)
            ->when($c['dirs'] !== null, fn ($q) => $q->where(fn ($w) => $w->where('auteur_id', $user->id)
                ->orWhere(fn ($x) => $x->where('portee_type', 'direction')->whereIn('portee_id', $c['dirs'] ?: [0]))));
    }

    public static function syntheseCourante(array $c, $user, ?int $id = null): ?SynthesePilotage
    {
        if ($id) {
            return self::synthesesVisibles($c, $user)->find($id);
        }

        return self::synthesesVisibles($c, $user)->where('periode_type', $c['periode']['type'])->where('periode_valeur', $c['periode']['valeur'])
            ->where('portee_type', $c['porteeType'])->where('portee_id', $c['porteeId'])->latest('id')->first();
    }

    public static function historiqueSyntheses(array $c, $user): Collection
    {
        return self::synthesesVisibles($c, $user)->with('auteur:id,name')->latest('id')->limit(6)->get();
    }
}
