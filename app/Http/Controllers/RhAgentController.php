<?php

namespace App\Http\Controllers;

use App\Models\DossierInfo;
use App\Models\JournalActivite;
use App\Models\ListeValeur;
use App\Models\Personnel;
use App\Models\PersonnelCompetence;
use App\Models\PersonnelFormation;
use App\Models\PieceJointe;
use App\Support\ChargeTravail;
use App\Support\CompletudeFiche;
use App\Support\HierarchieResponsables;
use App\Support\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Profil RH d'un agent : charge de travail, compétences, formations, validité du dossier, journal des modifications. */
class RhAgentController extends Controller
{
    private function estLuiMeme(Request $request, Personnel $agent): bool
    {
        return $request->user()->personnel_id !== null && (int) $request->user()->personnel_id === (int) $agent->id;
    }

    private function peutVoir(Request $request, Personnel $agent): bool
    {
        return $request->user()->hasRole('ADMIN|DG') || $this->estLuiMeme($request, $agent);
    }

    private function peutModifier(Request $request, Personnel $agent): bool
    {
        return $request->user()->hasRole('ADMIN') || $this->estLuiMeme($request, $agent);
    }

    public function show(Request $request, $id)
    {
        $agent = Personnel::with(['service.sousDirection.direction', 'service.directionDirecte', 'typeAgent', 'emploi', 'genre', 'user.profilAcces', 'competences', 'formations',
            'mandats' => fn ($q) => $q->actifA()->with(['direction', 'sousDirection.direction', 'service.sousDirection.direction'])])->findOrFail($id);
        abort_unless($this->peutVoir($request, $agent), 403, "Vous n'avez pas accès à ce profil.");

        $pieces = PieceJointe::where('attachable_type', $agent->getMorphClass())->where('attachable_id', $agent->id)->where('categorie', 'like', 'dossier\_%')->orderByDesc('id')->get();
        $infos = DossierInfo::whereIn('piece_jointe_id', $pieces->pluck('id'))->get()->keyBy('piece_jointe_id');
        $types = ListeValeur::where('liste', 'type_document')->pluck('libelle', 'code');
        $documents = $pieces->map(function ($p) use ($infos, $types) {
            $info = $infos->get($p->id) ?? new DossierInfo(['piece_jointe_id' => $p->id]);
            $code = preg_replace('/^dossier_/', '', (string) $p->categorie);

            return ['piece' => $p, 'info' => $info, 'etat' => $info->etat(), 'type' => $types[$code] ?? ucfirst(str_replace('_', ' ', $code))];
        });

        $journal = $request->user()->hasRole('ADMIN|DG')
            ? JournalActivite::where('parent_type', $agent->getMorphClass())->where('parent_id', $agent->id)->where('module', 'personnel')
                ->orderByDesc('created_at')->orderByDesc('id')->limit(80)->get()
            : collect();

        return view('rh.agent', [
            'agent' => $agent, 'charge' => ChargeTravail::pour($agent), 'completude' => CompletudeFiche::analyser($agent), 'rattachement' => $agent->rattachement(),
            'superieur' => HierarchieResponsables::superieurDirect($agent), 'documents' => $documents, 'journal' => $journal,
            'peutModifier' => $this->peutModifier($request, $agent), 'estAdmin' => $request->user()->hasRole('ADMIN'), 'voirJournal' => $request->user()->hasRole('ADMIN|DG'),
        ]);
    }

    public function storeCompetence(Request $request, $id)
    {
        $agent = Personnel::findOrFail($id);
        abort_unless($this->peutModifier($request, $agent), 403, 'Vous ne pouvez pas modifier ce profil.');
        $d = $request->validate(['libelle' => ['required', 'string', 'max:150'], 'niveau' => ['required', 'integer', 'between:1,4'], 'commentaire' => ['nullable', 'string', 'max:255']],
            ['libelle.required' => 'Indiquez la compétence.']);
        if ($agent->competences()->count() >= 40) {
            return redirect()->back()->withErrors(['libelle' => 'Maximum de 40 compétences par agent.']);
        }
        PersonnelCompetence::create($d + ['personnel_id' => $agent->id, 'cree_par' => $request->user()->id]);
        Journal::noter('personnel', 'modification', "Compétence ajoutée au profil de {$agent->nomComplet()} : {$d['libelle']}", $agent, $agent);

        return redirect()->route('rh.agent', $agent->id)->with('success', 'Compétence ajoutée.');
    }

    public function deleteCompetence(Request $request, $id)
    {
        $c = PersonnelCompetence::with('personnel')->findOrFail($id);
        abort_unless($this->peutModifier($request, $c->personnel), 403, 'Vous ne pouvez pas modifier ce profil.');
        Journal::noter('personnel', 'modification', "Compétence retirée du profil de {$c->personnel->nomComplet()} : {$c->libelle}", $c->personnel, $c->personnel);
        $c->delete();

        return redirect()->route('rh.agent', $c->personnel_id)->with('success', 'Compétence retirée.');
    }

    public function storeFormation(Request $request, $id)
    {
        $agent = Personnel::findOrFail($id);
        abort_unless($this->peutModifier($request, $agent), 403, 'Vous ne pouvez pas modifier ce profil.');
        $validator = Validator::make($request->all(), [
            'intitule' => ['required', 'string', 'max:200'], 'organisme' => ['nullable', 'string', 'max:200'], 'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'], 'duree_heures' => ['nullable', 'integer', 'between:1,5000'], 'observations' => ['nullable', 'string', 'max:255'],
        ], ['intitule.required' => "Indiquez l'intitulé de la formation.", 'date_fin.after_or_equal' => 'La fin ne peut pas précéder le début.']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($agent->formations()->count() >= 100) {
            return redirect()->back()->withErrors(['intitule' => 'Maximum de 100 formations par agent.']);
        }
        $d = $validator->validated();
        PersonnelFormation::create($d + ['certifiante' => $request->boolean('certifiante'), 'personnel_id' => $agent->id, 'cree_par' => $request->user()->id]);
        Journal::noter('personnel', 'modification', "Formation ajoutée au profil de {$agent->nomComplet()} : {$d['intitule']}", $agent, $agent);

        return redirect()->route('rh.agent', $agent->id)->with('success', 'Formation ajoutée.');
    }

    public function deleteFormation(Request $request, $id)
    {
        $f = PersonnelFormation::with('personnel')->findOrFail($id);
        abort_unless($this->peutModifier($request, $f->personnel), 403, 'Vous ne pouvez pas modifier ce profil.');
        Journal::noter('personnel', 'modification', "Formation retirée du profil de {$f->personnel->nomComplet()} : {$f->intitule}", $f->personnel, $f->personnel);
        $f->delete();

        return redirect()->route('rh.agent', $f->personnel_id)->with('success', 'Formation retirée.');
    }
}
