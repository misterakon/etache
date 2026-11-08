<?php

namespace App\Http\Controllers;

use App\Models\DossierInfo;
use App\Models\Fonction;
use App\Models\ListeValeur;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\PieceJointe;
use App\Support\ChargeTravail;
use App\Support\Journal;
use App\Support\OrganigrammeResponsables;
use App\Support\Reglages;
use App\Support\RhAlertes;
use App\Support\StatistiquesPersonnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Pilotage du personnel : organigramme interactif, tableau de bord, charge de travail, validité des dossiers. */
class RhPilotageController extends Controller
{
    // ------------------------------------------------------------------ organigramme
    public function organigramme(Request $request)
    {
        $donnees = OrganigrammeResponsables::construire();
        $agents = Personnel::nonArchives()->where('actif', true)->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom', 'service_id', 'fonction', 'fonction_id']);
        $parService = $agents->whereNotNull('service_id')->groupBy('service_id');

        $lignes = collect($donnees['lignes'])->map(function ($l) use ($parService, $agents) {
            $membres = match (true) {
                $l['niveau'] === MandatResponsable::NIVEAU_SERVICE => $parService->get($l['unite_id'], collect()),
                $l['niveau'] === MandatResponsable::NIVEAU_DG => $agents->whereNull('service_id')->whereNotNull('fonction_id'),
                default => collect(),
            };
            $l['membres'] = $membres->values();

            return $l;
        })->all();

        // Effectif cumulé d'une unité : ses agents et ceux des unités placées en dessous (clé hiérarchique « ordre »)
        foreach ($lignes as $i => $l) {
            $lignes[$i]['effectif'] = $l['niveau'] === MandatResponsable::NIVEAU_DG
                ? $agents->count()
                : collect($lignes)->filter(fn ($x) => str_starts_with($x['ordre'], $l['ordre']) && $x['niveau'] !== MandatResponsable::NIVEAU_DG)->sum(fn ($x) => $x['membres']->count());
        }

        $panneaux = collect($lignes)->map(fn ($l, $i) => [
            'nom' => $l['nom'], 'niveau' => $l['niveau'], 'unite_id' => $l['unite_id'], 'etat' => $l['etat'], 'effectif' => $l['effectif'],
            'responsable' => $l['responsable']?->nomComplet(), 'responsable_id' => $l['responsable']?->id,
            'responsable_photo' => $l['responsable']?->photo_url, 'responsable_initiales' => $l['responsable']?->initiales(),
            'responsable_email' => $l['responsable']?->email,
            'depuis' => $l['mandat']?->date_debut?->format('d/m/Y'), 'interim' => $l['etat'] === 'interim',
            'membres' => $l['membres']->map(fn ($m) => ['id' => $m->id, 'nom' => $m->nomComplet(), 'fonction' => $m->fonction, 'url' => route('dgepn.personnel.fiche', $m->id)])->all(),
        ])->all();

        $fonctions = Fonction::where('actif', true)->where('categorie', Fonction::CAT_MANDAT)->orderBy('ordre')->get(['id', 'libelle', 'niveau_mandat'])
            ->groupBy('niveau_mandat')->map(fn ($g) => $g->map(fn ($f) => ['id' => $f->id, 'libelle' => $f->libelle])->values())->all();

        return view('rh.organigramme', [
            'lignes' => $lignes, 'synthese' => $donnees['synthese'], 'panneaux' => $panneaux, 'fonctions' => $fonctions,
            'agents' => $agents, 'peutAffecter' => $request->user()->hasRole('ADMIN'),
        ]);
    }

    // ------------------------------------------------------------------ tableau de bord
    public function tableauDeBord()
    {
        RhAlertes::executerSiNecessaire();
        $stats = StatistiquesPersonnel::calculer();
        $charge = ChargeTravail::tous();
        $synthese = OrganigrammeResponsables::construire()['synthese'];

        $documents = DossierInfo::whereNotNull('valide_jusqu_au')->get();

        return view('rh.tableau_de_bord', [
            's' => $stats, 'vacants' => $synthese['sans'] + $synthese['inactifs'], 'interims' => $synthese['interims'], 'unites' => $synthese['unites'],
            'surcharges' => $charge->where('surcharge', true)->count(), 'seuil' => (int) Reglages::get('personnel.seuil_surcharge'),
            'docs_expires' => $documents->filter(fn ($d) => $d->etat() === 'expire')->count(), 'docs_bientot' => $documents->filter(fn ($d) => $d->etat() === 'bientot')->count(),
            'plusCharges' => $charge->take(5),
        ]);
    }

    // ------------------------------------------------------------------ charge de travail
    public function charge(Request $request)
    {
        RhAlertes::executerSiNecessaire();
        $toutes = ChargeTravail::tous();
        $lignes = $request->query('surcharge') === '1' ? $toutes->where('surcharge', true)->values() : $toutes;
        if ($request->filled('q')) {
            $m = mb_strtolower($request->query('q'));
            $lignes = $lignes->filter(fn ($l) => str_contains(mb_strtolower($l['personnel']->nomComplet()), $m))->values();
        }

        return view('rh.charge', [
            'lignes' => $lignes, 'seuil' => (int) Reglages::get('personnel.seuil_surcharge'),
            'kpis' => [
                'agents' => $toutes->count(), 'surcharges' => $toutes->where('surcharge', true)->count(),
                'en_retard' => $toutes->filter(fn ($l) => $l['retard'] > 0)->count(), 'sans_charge' => $toutes->where('charge', 0)->count(),
            ],
        ]);
    }

    // ------------------------------------------------------------------ validité des dossiers
    public function dossiers(Request $request)
    {
        RhAlertes::executerSiNecessaire();
        $pieces = PieceJointe::where('attachable_type', (new Personnel)->getMorphClass())->where('categorie', 'like', 'dossier\_%')->orderByDesc('id')->get();
        $infos = DossierInfo::whereIn('piece_jointe_id', $pieces->pluck('id'))->get()->keyBy('piece_jointe_id');
        $agents = Personnel::whereIn('id', $pieces->pluck('attachable_id')->unique())->get()->keyBy('id');
        $types = ListeValeur::where('liste', 'type_document')->pluck('libelle', 'code');

        $lignes = $pieces->map(function ($p) use ($infos, $agents, $types) {
            $info = $infos->get($p->id) ?? new DossierInfo(['piece_jointe_id' => $p->id]);
            $code = preg_replace('/^dossier_/', '', (string) $p->categorie);

            return ['piece' => $p, 'info' => $info, 'etat' => $info->etat(), 'agent' => $agents->get($p->attachable_id), 'type' => $types[$code] ?? ucfirst(str_replace('_', ' ', $code))];
        })->filter(fn ($l) => $l['agent'] && ! $l['agent']->estArchive());

        $compteurs = $lignes->countBy('etat');
        if ($request->filled('etat')) {
            $lignes = $lignes->where('etat', $request->query('etat'));
        }
        if ($request->filled('q')) {
            $m = mb_strtolower($request->query('q'));
            $lignes = $lignes->filter(fn ($l) => str_contains(mb_strtolower($l['agent']->nomComplet().' '.$l['type']), $m));
        }

        return view('rh.dossiers', [
            'lignes' => $lignes->sortBy(fn ($l) => [['expire' => 0, 'bientot' => 1, 'sans_date' => 3, 'valide' => 2][$l['etat']], $l['info']->valide_jusqu_au?->timestamp ?? PHP_INT_MAX])->values(),
            'compteurs' => $compteurs, 'jours' => (int) Reglages::get('personnel.rappel_dossier_jours'), 'peutModifier' => $request->user()->hasRole('ADMIN'),
        ]);
    }

    public function datesDossier(Request $request, $pieceId)
    {
        Gate::authorize('rh_dossiers.modifier');
        $piece = PieceJointe::where('attachable_type', (new Personnel)->getMorphClass())->where('categorie', 'like', 'dossier\_%')->findOrFail($pieceId);

        $validator = Validator::make($request->all(), [
            'date_document' => ['nullable', 'date', 'before_or_equal:today'], 'valide_jusqu_au' => ['nullable', 'date', 'after_or_equal:date_document'], 'observation' => ['nullable', 'string', 'max:255'],
        ], ['valide_jusqu_au.after_or_equal' => 'La fin de validité ne peut pas précéder la date du document.', 'date_document.before_or_equal' => 'La date du document ne peut pas être dans le futur.']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DossierInfo::updateOrCreate(['piece_jointe_id' => $piece->id], $validator->validated() + ['modifie_par' => $request->user()->id]);
        $agent = Personnel::find($piece->attachable_id);
        Journal::noter('personnel', 'modification', "Validité d'un document du dossier de {$agent?->nomComplet()} renseignée", $agent, $agent, ['piece' => $piece->id]);

        return redirect()->back()->with('success', 'Validité du document enregistrée.');
    }
}
