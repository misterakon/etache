<?php

namespace App\Support;

use App\Models\RoleAffectation;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TacheJustification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Exports de tâches : types d'export, requête (filtres de l'écran + droits) et colonnes.
 * Le rendu Excel / PDF est dans ExportExcel et exports/tableau_pdf.blade.php.
 */
class ExportTaches
{
    public const TYPES = [
        'toutes' => 'Toutes les tâches',
        'en_retard' => 'Tâches en retard',
        'non_justifies' => 'Retards non justifiés',
        'justifies' => 'Retards justifiés ou en attente de décision',
        'en_cours' => 'Tâches en cours',
        'terminees' => 'Tâches terminées',
        'a_venir' => 'Tâches à venir',
        'annulees' => 'Tâches annulées',
        'sans_responsable' => 'Tâches sans responsable',
    ];

    /** Délais proposés pour l'export « à venir » (Réglages > Exports). */
    public static function joursAVenir(): array
    {
        return array_map('intval', Reglages::get('exports.jours_a_venir'));
    }

    /** cle => [titre, présente dans le PDF ?]  (le PDF paysage ne porte que les colonnes essentielles) */
    private const COLONNES = [
        'code' => ['Code', true],
        'libelle' => ['Libellé', true],
        'axe' => ['Axe', false],
        'action_cle' => ['Action clé', false],
        'action_mesurable' => ['Action mesurable', false],
        'directions' => ['Directions porteuses', false],
        'responsable' => ['Responsable', true],
        'pilote' => ['Pilote', false],
        'contributeurs' => ['Contributeurs', false],
        'statut' => ['Statut', true],
        'priorite' => ['Priorité', false],
        'avancement' => ['Avancement (%)', true],
        'sous_taches' => ['Sous-tâches (faites/total)', false],
        'debut' => ['Début', false],
        'echeance' => ['Échéance', true],
        'jours_retard' => ['Jours de retard', true],
        'justification' => ['État de la justification', true],
        'reports' => ['Reports accordés', false],
        'budget' => ['Budget estimé (FCFA)', false],
        'indicateur' => ['Indicateur', false],
        'livrable' => ['Livrable attendu', false],
        'livrable_depose' => ['Livrable déposé', false],
    ];

    /** Même périmètre que l'écran : exercice, droits de l'utilisateur, filtres actifs. */
    public static function requete(string $type, Request $request, int $jours = 7): Builder
    {
        $statut = fn (array $codes) => fn (Builder $q) => $q->whereHas(
            'statutExecution', fn ($s) => $s->whereIn('code', $codes)
        );

        $query = Tache::query()
            ->with([
                'actionMesurable.actionCle.axe', 'actionMesurable.actionCle.direction',
                'actionMesurable.actionCle.directionsAssociees',
                'statutExecution', 'priorite', 'affectations.personnel', 'justifications',
            ])
            ->avecCompteSousTaches()
            ->dansExercice(FiltresTaches::exerciceId($request))
            ->visiblePar($request->user())
            ->filtrer(FiltresTaches::depuis($request))
            ->orderBy('code');

        return match ($type) {
            'en_retard' => $query->enRetardCalcule(),
            'non_justifies' => $query->retardNonJustifie(),
            'justifies' => $query->retardJustifie(),
            'en_cours' => $query->where($statut([StatutExecution::EN_COURS])),
            'terminees' => $query->where($statut([StatutExecution::TERMINEE])),
            'annulees' => $query->where($statut([StatutExecution::ANNULEE])),
            'sans_responsable' => $query->whereDoesntHave('responsables'),
            'a_venir' => $query->nonTerminees()->where(fn ($w) => $w
                ->where($statut([StatutExecution::NON_DEMARREE]))
                ->orWhereBetween('date_fin', [today()->toDateString(), today()->addDays($jours)->toDateString()])),
            default => $query,
        };
    }

    /** @return array<string,string> cle => titre, pour le format demandé ('xlsx' = tout, 'pdf' = essentiel) */
    public static function colonnes(string $format): array
    {
        return collect(self::COLONNES)
            ->filter(fn ($c) => $format !== 'pdf' || $c[1])
            ->map(fn ($c) => $c[0])
            ->all();
    }

    /** Lignes (tableaux de valeurs) dans l'ordre des colonnes demandées. */
    public static function lignes(Collection $taches, array $cles): array
    {
        $roles = RoleAffectation::pluck('id', 'code');
        $agents = fn ($t, string $code) => VuesTachesDonnees::agents($t, $roles[$code] ?? null)
            ->map(fn ($p) => $p->nomComplet())->implode(', ');
        $date = fn ($d) => $d?->format('d/m/Y') ?? '';

        return $taches->map(function ($t) use ($cles, $agents, $date) {
            $ac = $t->actionMesurable?->actionCle;
            $valeurs = [
                'code' => $t->code,
                'libelle' => $t->libelle,
                'axe' => trim(($ac?->axe?->code ?? '').' '.($ac?->axe?->libelle ?? '')),
                'action_cle' => trim(($ac?->code ?? '').' '.($ac?->libelle ?? '')),
                'action_mesurable' => trim(($t->actionMesurable?->code ?? '').' '.($t->actionMesurable?->libelle ?? '')),
                'directions' => $t->directionsPorteuses()->pluck('nom')->implode(', '),
                'responsable' => $agents($t, RoleAffectation::RESPONSABLE),
                'pilote' => $agents($t, RoleAffectation::PILOTE),
                'contributeurs' => $agents($t, RoleAffectation::CONTRIBUTEUR),
                'statut' => $t->statutExecution?->libelle ?? '',
                'priorite' => $t->priorite?->libelle ?? '',
                'avancement' => (int) $t->avancement_pourcentage,
                'sous_taches' => ($t->sous_taches_count ?? 0) > 0 ? ($t->sous_taches_cochees_count ?? 0).'/'.$t->sous_taches_count : '',
                'debut' => $date($t->date_debut),
                'echeance' => $date($t->date_fin),
                'jours_retard' => $t->estEnRetard() ? (int) abs($t->date_fin->copy()->startOfDay()->diffInDays(today())) : '',
                'justification' => self::etatJustification($t),
                'reports' => $t->justifications
                    ->where('type', TacheJustification::TYPE_REPORT)
                    ->where('statut', TacheJustification::APPROUVEE)->count(),
                'budget' => (float) $t->budget_estime,
                'indicateur' => $t->indicateur,
                'livrable' => $t->livrable,
                'livrable_depose' => ($t->pieces_jointes_count ?? 0) > 0 ? 'Oui' : 'Non',
            ];

            return array_map(fn ($c) => $valeurs[$c] ?? '', $cles);
        })->all();
    }

    /**
     * Cartes de chiffres clés et tableaux de synthèse (PDF et Excel).
     * Option du 6e argument de ExportExcel::telecharger() et du 6e argument de ExportPdf::tableau().
     */
    public static function synthese(Collection $taches): array
    {
        $total = $taches->count();
        $part = fn (int $n) => $total ? number_format($n * 100 / $total, 1, ',', ' ').' %' : '0 %';
        $termine = $taches->filter(fn ($t) => $t->statutExecution?->code === StatutExecution::TERMINEE)->count();
        $retard = $taches->filter(fn ($t) => $t->estEnRetard())->count();
        $moyenne = $total ? round((float) $taches->avg('avancement_pourcentage'), 1) : 0;

        $repartition = fn (callable $cle) => $taches->groupBy(fn ($t) => $cle($t) ?: 'Non renseigné')
            ->map(fn ($g, $k) => [$k, $g->count(), $part($g->count())])
            ->sortByDesc(1)->values()->all();

        return [
            'cartes' => [
                ['libelle' => 'Tâches', 'valeur' => $total],
                ['libelle' => 'Terminées', 'valeur' => $termine],
                ['libelle' => 'En retard', 'valeur' => $retard],
                ['libelle' => 'Avancement moyen', 'valeur' => $moyenne, 'unite' => '%'],
            ],
            'synthese' => [
                ['titre' => 'Répartition par statut', 'entetes' => ['Statut', 'Tâches', 'Part'], 'lignes' => $repartition(fn ($t) => $t->statutExecution?->libelle)],
                ['titre' => 'Répartition par priorité', 'entetes' => ['Priorité', 'Tâches', 'Part'], 'lignes' => $repartition(fn ($t) => $t->priorite?->libelle)],
            ],
        ];
    }

    private static function etatJustification(Tache $t): string
    {
        if (! $t->estEnRetard()) {
            return '';
        }
        $j = $t->justifications;
        if ($j->contains('statut', TacheJustification::EN_ATTENTE)) {
            return 'Demande en attente';
        }
        $valable = $j->contains(fn ($x) => $x->statut === TacheJustification::APPROUVEE
            && $x->type === TacheJustification::TYPE_JUSTIFICATION
            && $x->echeance_actuelle?->isSameDay($t->date_fin));

        return $valable ? 'Justifié' : 'Non justifié';
    }
}
