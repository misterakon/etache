<?php

namespace App\Support;

use App\Models\Axe;
use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Models\Diligence;
use App\Models\Direction;
use App\Models\Exercice;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TacheHistorique;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Chiffres du pilotage de la feuille de route (F8 / F12) : situation, période (mois, trimestre, année), par axe,
 * par direction, courbe attendu / réalisé, retards, courrier. Tout est calculé à partir des données courantes et de
 * l'historique des statuts : aucune photo n'est nécessaire pour la courbe cumulée.
 */
class AnalysesPilotage
{
    public const TYPES = ['annee' => 'Année', 'trimestre' => 'Trimestre', 'mois' => 'Mois'];

    public const MOIS_COURTS = [1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Aoû', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'];

    public const MOIS = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];

    public static function periode(string $type, ?int $valeur, Exercice $exercice): array
    {
        $annee = (int) $exercice->annee;

        if ($type === 'mois') {
            $m = min(12, max(1, $valeur ?? (int) now()->month));
            $debut = Carbon::create($annee, $m, 1)->startOfDay();

            return ['type' => 'mois', 'valeur' => $m, 'debut' => $debut, 'fin' => $debut->copy()->endOfMonth(), 'libelle' => ucfirst(self::MOIS[$m]).' '.$annee];
        }
        if ($type === 'trimestre') {
            $q = min(4, max(1, $valeur ?? (int) ceil(now()->month / 3)));
            $debut = Carbon::create($annee, 3 * $q - 2, 1)->startOfDay();

            return ['type' => 'trimestre', 'valeur' => $q, 'debut' => $debut, 'fin' => $debut->copy()->addMonths(2)->endOfMonth(), 'libelle' => ($q === 1 ? '1er' : $q.'e').' trimestre '.$annee];
        }

        return [
            'type' => 'annee', 'valeur' => null, 'debut' => ($exercice->date_debut ?? Carbon::create($annee, 1, 1))->copy()->startOfDay(),
            'fin' => ($exercice->date_fin ?? Carbon::create($annee, 12, 31))->copy()->endOfDay(), 'libelle' => 'Exercice '.$annee,
        ];
    }

    /** Libellé lisible du périmètre analysé. */
    public static function libellePortee(string $type, ?int $id, ?array $directionsAutorisees): string
    {
        return match (true) {
            $type === 'axe' && $id => 'Axe : '.(Axe::find($id)?->libelle ?? '—'),
            $type === 'direction' && $id => 'Direction : '.(Direction::find($id)?->nom ?? '—'),
            $directionsAutorisees !== null => 'Mes directions',
            default => 'Toute la structure',
        };
    }

    /**
     * @param  list<int>|null  $directionsAutorisees  null = tout (ADMIN, DG)
     */
    public static function calculer(Exercice $exercice, array $periode, string $porteeType = 'global', ?int $porteeId = null, ?array $directionsAutorisees = null): array
    {
        $codes = StatutExecution::orderBy('code')->get();
        $libTerminee = $codes->firstWhere('code', StatutExecution::TERMINEE)?->libelle;

        $taches = Tache::with(['statutExecution', 'justifications', 'actionMesurable.actionCle.axe', 'actionMesurable.actionCle.direction', 'actionMesurable.actionCle.directionsAssociees'])
            ->whereHas('actionMesurable.actionCle.axe', fn ($q) => $q->where('exercice_id', $exercice->id))->get();

        // Date de fin réelle d'une tâche terminée : dernier passage au statut « Terminée » dans l'historique
        $completions = $libTerminee
            ? TacheHistorique::whereIn('tache_id', $taches->pluck('id'))->where('champ', 'statut_execution_id')->where('nouvelle_valeur', $libTerminee)
                ->orderBy('created_at')->get()->groupBy('tache_id')->map(fn ($g) => $g->last()->created_at)
            : collect();

        $lignes = $taches->map(function (Tache $t) use ($completions) {
            $code = $t->statutExecution?->code;
            $ac = $t->actionMesurable?->actionCle;
            $terminee = $code === StatutExecution::TERMINEE;

            return [
                'tache' => $t, 'code' => $code, 'terminee' => $terminee, 'annulee' => $code === StatutExecution::ANNULEE,
                // une tâche soumise à temps et validée plus tard compte comme terminée à temps.
                'completion' => $terminee ? ($t->cloture_soumise_le ?? $completions[$t->id] ?? $t->derniere_mise_a_jour_le ?? $t->updated_at) : null,
                'retard' => $t->estEnRetard(), 'just' => $t->etatJustification(), 'axe' => $ac?->axe, 'direction' => $ac?->direction,
                'dirs' => $ac ? $ac->directionsPorteuses()->pluck('id')->all() : [], 'avancement' => (float) ($t->avancement_pourcentage ?? 0),
                'fin' => $t->date_fin, 'budget' => (float) $t->budget_estime,
            ];
        })->filter(function (array $l) use ($porteeType, $porteeId, $directionsAutorisees) {
            if ($directionsAutorisees !== null && array_intersect($l['dirs'], $directionsAutorisees) === []) {
                return false;
            }
            if ($porteeType === 'axe' && $porteeId) {
                return $l['axe']?->id === $porteeId;
            }
            if ($porteeType === 'direction' && $porteeId) {
                return in_array($porteeId, $l['dirs'], true);
            }

            return true;
        })->values();

        $actives = $lignes->reject(fn ($l) => $l['annulee'])->values();
        $total = $actives->count();

        $agreger = function (Collection $l): array {
            $n = $l->count();
            $term = $l->where('terminee', true)->count();
            $parCode = fn (string $c) => $l->where('code', $c)->count();

            return [
                'total' => $n, 'terminees' => $term, 'en_cours' => $parCode(StatutExecution::EN_COURS), 'non_demarrees' => $parCode(StatutExecution::NON_DEMARREE),
                'bloquees' => $parCode(StatutExecution::BLOQUEE), 'suspendues' => $parCode(StatutExecution::SUSPENDUE),
                'en_retard' => $l->where('retard', true)->count(), 'non_justifies' => $l->where('just', 'non_justifie')->count(),
                'avancement' => $n ? round($l->avg('avancement'), 1) : 0.0, 'taux' => $n ? round($term / $n * 100, 1) : 0.0,
                'budget' => round($l->sum('budget')), 'budget_realise' => round($l->where('terminee', true)->sum('budget')),
            ];
        };

        // --- Période
        $debut = $periode['debut'];
        $fin = $periode['fin'];
        $ref = $fin->lt(now()) ? $fin->copy() : now();
        $echeances = $actives->filter(fn ($l) => $l['fin'] && $l['fin']->copy()->startOfDay()->betweenIncluded($debut, $fin));
        $terminees = $actives->filter(fn ($l) => $l['terminee'] && $l['completion'] && $l['completion']->betweenIncluded($debut, $fin));
        $aTemps = $terminees->filter(fn ($l) => ! $l['fin'] || $l['completion']->lte($l['fin']->copy()->endOfDay()));
        $respectees = $echeances->filter(fn ($l) => $l['terminee'] && $l['completion'] && $l['completion']->lte($l['fin']->copy()->endOfDay()));
        $enRetardFin = $actives->filter(fn ($l) => $l['fin'] && $l['fin']->copy()->endOfDay()->lte($ref) && ! ($l['terminee'] && $l['completion'] && $l['completion']->lte($ref)));

        // --- Courbe attendu / réalisé (cumul par fin de mois)
        $debutEx = ($exercice->date_debut ?? Carbon::create((int) $exercice->annee, 1, 1))->copy()->startOfMonth();
        $finEx = ($exercice->date_fin ?? Carbon::create((int) $exercice->annee, 12, 31))->copy()->endOfMonth();
        $courbe = [];
        for ($m = $debutEx->copy(); $m->lte($finEx) && count($courbe) < 24; $m->addMonth()) {
            $finMois = $m->copy()->endOfMonth();
            $courbe[] = [
                'mois' => self::MOIS_COURTS[$m->month].($m->month === 1 ? ' '.$m->format('y') : ''),
                'attendu' => $total ? round($actives->filter(fn ($l) => $l['fin'] && $l['fin']->copy()->endOfDay()->lte($finMois))->count() / $total * 100, 1) : 0,
                'realise' => $total && $m->lte(now()->endOfMonth()) ? round($actives->filter(fn ($l) => $l['terminee'] && $l['completion'] && $l['completion']->lte($finMois))->count() / $total * 100, 1) : null,
            ];
        }

        // --- Par axe et par direction (porteuse principale)
        $parAxe = $actives->filter(fn ($l) => $l['axe'])->groupBy(fn ($l) => $l['axe']->id)->map(fn ($g) => ['id' => $g->first()['axe']->id, 'code' => $g->first()['axe']->code, 'libelle' => $g->first()['axe']->libelle] + $agreger($g))
            ->sortBy('code')->values()->all();

        $parDirection = $actives->groupBy(fn ($l) => $l['direction']?->id ?? 0)->map(fn ($g, $id) => ['id' => (int) $id, 'libelle' => $g->first()['direction']?->nom ?? 'Non affectée'] + $agreger($g))
            ->when($directionsAutorisees !== null, fn ($c) => $c->filter(fn ($r) => in_array($r['id'], $directionsAutorisees, true)))
            ->sortByDesc('en_retard')->values()->all();

        $retards = $actives->filter(fn ($l) => $l['retard'])->map(fn ($l) => [
            'code' => $l['tache']->code, 'libelle' => $l['tache']->libelle, 'axe' => $l['axe']?->libelle, 'direction' => $l['direction']?->nom,
            'echeance' => $l['fin']?->format('d/m/Y'), 'jours' => (int) $l['fin']->diffInDays(today()), 'justification' => ['attente' => 'En attente', 'justifie' => 'Justifié', 'non_justifie' => 'Non justifié'][$l['just']] ?? '',
            'avancement' => $l['avancement'],
        ])->sortByDesc('jours')->take(80)->values()->all();

        $semaines = [];
        $lundi = today()->startOfWeek();
        for ($k = 0; $k < 8; $k++) {
            $d1 = $lundi->copy()->addWeeks($k);
            $d2 = $d1->copy()->endOfWeek();
            $semaines[] = ['libelle' => $k === 0 ? 'Cette semaine' : 'S+'.$k, 'valeur' => $actives->filter(fn ($l) => ! $l['terminee'] && $l['fin'] && $l['fin']->betweenIncluded($d1, $d2))->count()];
        }

        $statuts = $codes->map(fn ($s) => ['libelle' => $s->libelle, 'couleur' => $s->couleur ?: '#8A94A6', 'valeur' => $actives->where('code', $s->code)->count()])->filter(fn ($s) => $s['valeur'] > 0)->values()->all();

        return [
            'exercice' => $exercice, 'periode' => $periode, 'portee_type' => $porteeType, 'portee_id' => $porteeId,
            'portee' => self::libellePortee($porteeType, $porteeId, $directionsAutorisees),
            'situation' => $agreger($actives), 'annulees' => $lignes->where('annulee', true)->count(),
            'periode_chiffres' => [
                'echeances' => $echeances->count(), 'terminees' => $terminees->count(), 'a_temps' => $aTemps->count(),
                'respectees' => $respectees->count(), 'taux_respect' => $echeances->count() ? round($respectees->count() / $echeances->count() * 100, 1) : null,
                'retard_fin_periode' => $enRetardFin->count(),
            ],
            'courbe' => $courbe, 'semaines' => $semaines, 'par_axe' => $parAxe, 'par_direction' => $parDirection, 'statuts' => $statuts, 'retards' => $retards,
            'courrier' => $porteeType === 'global' && $directionsAutorisees === null ? self::courrier((int) $exercice->annee) : null,
            'genere_le' => now(),
        ];
    }

    /** Courrier et diligences de l'année : volumes par mois, retards, délai moyen de traitement (global uniquement). */
    private static function courrier(int $annee): array
    {
        $parMois = fn ($requete) => collect(range(1, 12))->map(fn ($m) => (int) ($requete->get($m) ?? 0))->all();
        $recus = Courrier::arrivee()->whereYear('date_reception', $annee)->get(['date_reception'])->countBy(fn ($c) => (int) $c->date_reception->month);
        $departs = CourrierDepart::whereYear('date_depart', $annee)->get(['date_depart'])->countBy(fn ($c) => (int) $c->date_depart->month);
        $traitees = Diligence::where('statut', 'solde')->whereYear('solde_le', $annee)->get(['created_at', 'solde_le']);
        $ouvertes = Diligence::ouvertes()->get(['echeance']);

        return [
            'recus' => $parMois($recus), 'departs' => $parMois($departs), 'total_recus' => $recus->sum(), 'total_departs' => $departs->sum(),
            'diligences_ouvertes' => $ouvertes->count(), 'diligences_en_retard' => $ouvertes->filter(fn ($d) => $d->echeance->lt(today()))->count(),
            'diligences_traitees' => $traitees->count(),
            'statuts_diligences' => Diligence::query()->selectRaw('statut, COUNT(*) as n')->groupBy('statut')->pluck('n', 'statut')->all(),
            'delai_moyen' => $traitees->isNotEmpty() ? round($traitees->avg(fn ($d) => $d->created_at->diffInDays($d->solde_le)), 1) : null,
        ];
    }
}
