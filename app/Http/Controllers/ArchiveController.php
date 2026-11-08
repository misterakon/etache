<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Support\CourrierDroits;
use App\Support\DepartDroits;
use App\Support\Journal;
use App\Support\Reglages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Archivage : un courrier traité (ou un départ) est « classé » avec son n° d'archives (saisi à la main) et une date
 * « à conserver jusqu'au ». Aucune suppression automatique : l'échéance sert uniquement d'information.
 */
class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless(CourrierDroits::peutConsulterRegistre($user) || DepartDroits::peutConsulter($user), 403, "Vous n'avez pas accès aux archives.");
        $sens = $request->query('sens') === 'depart' ? 'depart' : 'arrivee';

        $arrivees = Courrier::arrivee()->visiblePar($user)->where('statut', 'archive');
        $departs = CourrierDepart::visiblePar($user)->where('statut', 'archive');

        $liste = $sens === 'depart' ? clone $departs : clone $arrivees;
        $liste->when($request->filled('q'), function ($q) use ($request, $sens) {
            $m = '%'.$request->query('q').'%';
            $q->where(fn ($w) => $sens === 'depart'
                ? $w->where('numero', 'like', $m)->orWhere('destinataire', 'like', $m)->orWhere('objet', 'like', $m)->orWhere('numero_archives', 'like', $m)
                : $w->where('numero', 'like', $m)->orWhere('expediteur', 'like', $m)->orWhere('objet', 'like', $m)->orWhere('numero_archives', 'like', $m));
        })->when($request->query('conservation') === 'depassee', fn ($q) => $q->whereDate('a_conserver_jusqu_au', '<', today()))
            ->when($request->query('conservation') === 'proche', fn ($q) => $q->whereBetween('a_conserver_jusqu_au', [today(), today()->addYear()]))
            ->when($request->query('conservation') === 'a_renseigner', fn ($q) => $q->whereNull('numero_archives'));

        $prets = Courrier::arrivee()->visiblePar($user)->where('statut', 'solde')->orderByDesc('id')->limit(30)->get();

        return view('courrier.archives', [
            'sens' => $sens,
            'lignes' => $liste->orderByDesc('archive_le')->orderByDesc('id')->paginate(20)->withQueryString(),
            'kpis' => [
                'arrivee' => (clone $arrivees)->count(),
                'depart' => (clone $departs)->count(),
                'a_renseigner' => (clone $arrivees)->whereNull('numero_archives')->count() + (clone $departs)->whereNull('numero_archives')->count(),
                'depassees' => (clone $arrivees)->whereDate('a_conserver_jusqu_au', '<', today())->count() + (clone $departs)->whereDate('a_conserver_jusqu_au', '<', today())->count(),
            ],
            'prets' => $prets,
            'peutClasser' => DepartDroits::peutClasser($user),
            'conservationAns' => (int) Reglages::get('courrier.conservation_ans'),
        ]);
    }

    public function classerCourrier(Request $request, $id)
    {
        $courrier = Courrier::findOrFail($id);
        abort_unless(DepartDroits::peutClasser($request->user()) && CourrierDroits::peutVoir($courrier, $request->user()), 403, "Vous ne pouvez pas classer ce courrier.");

        if (! in_array($courrier->statut, ['solde', 'archive'], true)) {
            return redirect()->back()->withErrors(['numero_archives' => 'Seul un courrier traité peut être classé.']);
        }
        $d = $this->valider($request);
        if ($d instanceof \Illuminate\Http\RedirectResponse) {
            return $d;
        }

        $courrier->update(['statut' => 'archive', 'archive_le' => $courrier->archive_le ?? now(), 'archive_par' => $request->user()->id, 'numero_archives' => $d['numero_archives'], 'a_conserver_jusqu_au' => $d['a_conserver_jusqu_au'] ?? null]);
        Journal::noter('courrier', 'archivage', "Courrier {$courrier->numero} classé — n° d'archives {$d['numero_archives']}".(! empty($d['a_conserver_jusqu_au']) ? ", à conserver jusqu'au ".\Carbon\Carbon::parse($d['a_conserver_jusqu_au'])->format('d/m/Y') : ''), $courrier, $courrier);

        return redirect()->back()->with('success', "Courrier {$courrier->numero} classé (n° d'archives {$d['numero_archives']}).");
    }

    public function classerDepart(Request $request, $id)
    {
        $depart = CourrierDepart::findOrFail($id);
        abort_unless(DepartDroits::peutClasser($request->user()) && DepartDroits::peutVoir($depart, $request->user()), 403, "Vous ne pouvez pas classer ce courrier départ.");
        $d = $this->valider($request);
        if ($d instanceof \Illuminate\Http\RedirectResponse) {
            return $d;
        }

        $depart->update(['statut' => 'archive', 'archive_le' => $depart->archive_le ?? now(), 'archive_par' => $request->user()->id, 'numero_archives' => $d['numero_archives'], 'a_conserver_jusqu_au' => $d['a_conserver_jusqu_au'] ?? null]);
        Journal::noter('courrier', 'archivage', "Courrier départ {$depart->numero} classé — n° d'archives {$d['numero_archives']}", $depart, $depart->courrier);

        return redirect()->back()->with('success', "Courrier départ {$depart->numero} classé (n° d'archives {$d['numero_archives']}).");
    }

    private function valider(Request $request): array|\Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'numero_archives' => ['required', 'string', 'max:60'],
            'a_conserver_jusqu_au' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'numero_archives.required' => "Saisissez le n° d'archives (référence de rangement : boîte, classeur, rayon…).",
            'a_conserver_jusqu_au.after_or_equal' => "La date de conservation ne peut pas être dans le passé.",
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('ouvrir_classement', true);
        }

        return $validator->validated();
    }
}
