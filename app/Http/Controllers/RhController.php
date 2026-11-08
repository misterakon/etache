<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\User;
use App\Support\ArchivageAgent;
use App\Support\CompletudeFiche;
use App\Support\CreationComptes;
use App\Support\DoublonsPersonnel;
use App\Support\ExportExcel;
use App\Support\Journal;
use App\Support\RhAlertes;
use App\Support\StatistiquesPersonnel;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Qualité des fiches du personnel : complétude, doublons, archives, comptes utilisateurs. */
class RhController extends Controller
{
    // ------------------------------------------------------------------ fiches incomplètes
    private function lignesQualite(Request $request)
    {
        $agents = Personnel::nonArchives()->where('actif', true)
            ->with(['service.sousDirection.direction', 'service.directionDirecte', 'mandats' => fn ($q) => $q->actifA()->with(['direction', 'sousDirection.direction', 'service.sousDirection.direction'])])
            ->orderBy('nom')->orderBy('prenom')->get();

        $lignes = $agents->map(fn ($a) => ['agent' => $a, 'direction' => StatistiquesPersonnel::direction($a)] + CompletudeFiche::analyser($a));

        $niveau = $request->query('niveau', 'essentiels');
        $lignes = match ($niveau) {
            'toutes' => $lignes->filter(fn ($l) => $l['manquants'] !== []),
            'completes' => $lignes->filter(fn ($l) => $l['manquants'] === []),
            default => $lignes->filter(fn ($l) => $l['essentiels'] !== []),
        };
        if ($request->filled('q')) {
            $m = DoublonsPersonnel::norme($request->query('q'));
            $lignes = $lignes->filter(fn ($l) => str_contains(DoublonsPersonnel::norme($l['agent']->nom.' '.$l['agent']->prenom.' '.$l['agent']->matricule), $m));
        }
        if ($request->filled('direction')) {
            $nom = Direction::find($request->integer('direction'))?->nom;
            $lignes = $lignes->filter(fn ($l) => $l['direction'] === $nom);
        }

        return [$agents, $lignes->sortBy('score')->values()];
    }

    public function qualite(Request $request)
    {
        RhAlertes::executerSiNecessaire();
        [$agents, $lignes] = $this->lignesQualite($request);
        $toutes = $agents->map(fn ($a) => CompletudeFiche::analyser($a));

        return view('rh.qualite', [
            'lignes' => $lignes,
            'kpis' => [
                'agents' => $agents->count(),
                'incompletes' => $toutes->filter(fn ($c) => $c['essentiels'] !== [])->count(),
                'completes' => $toutes->filter(fn ($c) => $c['manquants'] === [])->count(),
                'moyenne' => $toutes->isNotEmpty() ? (int) round($toutes->avg('score')) : 0,
            ],
            'directions' => Direction::orderBy('nom')->get(['id', 'nom']),
            'niveau' => $request->query('niveau', 'essentiels'),
        ]);
    }

    public function qualiteExport(Request $request)
    {
        [, $lignes] = $this->lignesQualite($request);
        Journal::noter('exports', 'export', 'Export des fiches du personnel à compléter : '.$lignes->count().' fiche(s)', null, null, ['filtres' => $request->query()]);

        return ExportExcel::telecharger(
            'fiches_a_completer_'.now()->format('Ymd_His').'.xlsx', 'Fiches du personnel à compléter',
            ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.$lignes->count().' fiche(s)'],
            ['Matricule', 'Nom', 'Prénom', 'Direction', 'Service', 'Complétude', 'Champs essentiels manquants', 'Autres champs manquants'],
            $lignes->map(fn ($l) => [
                $l['agent']->matricule, $l['agent']->nom, $l['agent']->prenom, $l['direction'], $l['agent']->service?->nom, $l['score'].' %',
                implode(', ', $l['essentiels']), implode(', ', array_values(array_diff($l['manquants'], $l['essentiels']))),
            ])->all()
        );
    }

    // ------------------------------------------------------------------ doublons
    public function doublons()
    {
        return view('rh.doublons', ['groupes' => DoublonsPersonnel::groupes()]);
    }

    // ------------------------------------------------------------------ archives
    public function archives(Request $request)
    {
        $agents = Personnel::archives()->with(['service:id,nom', 'user:id,personnel_id,actif'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $m = '%'.$request->query('q').'%';
                $q->where(fn ($w) => $w->where('nom', 'like', $m)->orWhere('prenom', 'like', $m)->orWhere('matricule', 'like', $m));
            })->orderByDesc('archive_le')->paginate(20)->withQueryString();

        return view('rh.archives', ['agents' => $agents, 'peutRestaurer' => $request->user()->hasRole('ADMIN')]);
    }

    public function archiver(Request $request, $id)
    {
        Gate::authorize('rh_archives.archiver');
        $motif = $request->validate(['motif' => ['required', 'string', 'max:255']], ['motif.required' => "Indiquez le motif de l'archivage."])['motif'];
        $agent = Personnel::findOrFail($id);

        try {
            $r = ArchivageAgent::archiver($agent, $motif, $request->user());
        } catch (DomainException $e) {
            return redirect()->back()->withErrors(['archivage' => $e->getMessage()]);
        }

        return redirect()->route('rh.archives')->with('success', "{$agent->nomComplet()} est archivé. {$r['mandats']} poste(s) terminé(s), {$r['comptes']} compte(s) désactivé(s)".($r['taches'] ? ", {$r['taches']} tâche(s) ouverte(s) à réaffecter." : '.'));
    }

    public function restaurer(Request $request, $id)
    {
        Gate::authorize('rh_archives.restaurer');
        $agent = Personnel::archives()->findOrFail($id);
        try {
            ArchivageAgent::restaurer($agent, $request->user());
        } catch (DomainException $e) {
            return redirect()->back()->withErrors(['archivage' => $e->getMessage()]);
        }

        return redirect()->route('rh.archives')->with('success', "{$agent->nomComplet()} est restauré. Son compte, s'il existe, reste désactivé : réactivez-le dans « Comptes utilisateurs ».");
    }

    // ------------------------------------------------------------------ comptes
    public function comptes(Request $request)
    {
        $sansCompte = Personnel::nonArchives()->where('actif', true)->whereDoesntHave('user')->with(['service:id,nom', 'typeAgent:id,libelle'])->orderBy('nom')->orderBy('prenom')->get();
        $adesactiver = User::where('actif', true)->whereNotNull('personnel_id')
            ->whereHas('personnel', fn ($p) => $p->where(fn ($w) => $w->where('actif', false)->orWhereNotNull('archive_le')))->with('personnel')->get();

        return view('rh.comptes', [
            'sansCompte' => $sansCompte,
            'adesactiver' => $adesactiver,
            'profils' => ProfilAcces::where('actif', true)->orderBy('libelle')->get(),
            'profilDefaut' => ProfilAcces::bySlug(ProfilAcces::AGENT)?->id,
            'peutCreer' => $request->user()->hasRole('ADMIN'),
            'resultat' => session('rh_resultat'),
            'avecMotDePasse' => (bool) session('rh_comptes_crees'),
        ]);
    }

    public function comptesCreer(Request $request)
    {
        Gate::authorize('rh_comptes.creer');
        $d = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:300'], 'ids.*' => ['integer'], 'profil_id' => ['required', 'exists:profils_acces,id'],
        ], ['ids.required' => 'Cochez au moins un agent.']);
        $profil = ProfilAcces::findOrFail($d['profil_id']);
        if ($profil->slug === ProfilAcces::ADMINISTRATEUR) {
            return redirect()->back()->withErrors(['profil_id' => 'Les comptes administrateur se créent un par un dans « Comptes utilisateurs ».']);
        }

        $agents = Personnel::nonArchives()->where('actif', true)->whereIn('id', $d['ids'])->get();
        $r = CreationComptes::enMasse($agents, $profil);
        Journal::noter('personnel', 'creation', 'Création de comptes en masse : '.count($r['crees']).' créé(s), '.count($r['ignores']).' ignoré(s) (profil '.$profil->libelle.')', null, null, ['profil' => $profil->slug]);
        session(['rh_comptes_crees' => $r['crees']]);

        return redirect()->route('rh.comptes')->with('rh_resultat', ['crees' => count($r['crees']), 'ignores' => $r['ignores']]);
    }

    /** Les mots de passe provisoires ne sont jamais enregistrés en clair : ce fichier n'est téléchargeable qu'une fois, juste après la création. */
    public function comptesTelecharger(Request $request)
    {
        $crees = session()->pull('rh_comptes_crees');
        abort_unless($crees, 404, 'Aucun compte récemment créé : ce fichier ne peut être téléchargé qu\'une fois, juste après la création.');
        Journal::noter('exports', 'export', 'Fichier des comptes créés (mots de passe provisoires) téléchargé : '.count($crees).' compte(s)');

        return ExportExcel::telecharger(
            'comptes_crees_'.now()->format('Ymd_His').'.xlsx', 'Comptes créés : mots de passe provisoires',
            ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name, 'CONFIDENTIEL : à remettre à chaque agent, puis à supprimer. Chaque agent doit changer son mot de passe à la première connexion.'],
            ['Agent', 'Matricule', 'E-mail (identifiant)', 'Mot de passe provisoire', 'Profil'],
            collect($crees)->map(fn ($c) => [$c['nom'], $c['matricule'], $c['email'], $c['mot_de_passe'], $c['profil']])->all()
        );
    }

    public function comptesDesactiver(Request $request)
    {
        Gate::authorize('rh_comptes.desactiver');
        $n = User::where('actif', true)->whereNotNull('personnel_id')->where('id', '!=', $request->user()->id)
            ->whereHas('personnel', fn ($p) => $p->where(fn ($w) => $w->where('actif', false)->orWhereNotNull('archive_le')))->update(['actif' => false]);
        Journal::noter('personnel', 'modification', "Désactivation de {$n} compte(s) d'agents inactifs ou archivés");

        return redirect()->route('rh.comptes')->with('success', "{$n} compte(s) désactivé(s).");
    }
}
