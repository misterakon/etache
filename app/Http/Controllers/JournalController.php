<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use App\Models\User;
use App\Support\ExportExcel;
use App\Support\Journal;
use App\Support\Reglages;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->peut('journal.voir'), 403, "Vous n'avez pas le droit d'accéder au journal d'activité.");

        $requete = JournalActivite::query()
            ->orderByDesc('created_at')->orderByDesc('id')
            ->when($request->filled('du'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('au')))
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->query('module')))
            ->when($request->filled('evenement'), fn ($q) => $q->where('evenement', $request->query('evenement')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('q'), fn ($q) => $q->where('resume', 'like', '%'.$request->query('q').'%'));

        if ($request->query('export') === 'xlsx') {
            $entrees = (clone $requete)->limit(Reglages::get('journal.export_max_lignes'))->get();
            $lignes = $entrees->map(fn ($e) => [
                $e->created_at->format('d/m/Y H:i:s'), $e->acteur_nom ?? '—', $e->libelleModule(), $e->libelleEvenement(), $e->resume,
            ])->all();

            Journal::noter('exports', 'export', "Export du journal d'activité : ".count($lignes).' ligne(s)', null, null, ['filtres' => $request->except('export')]);

            return ExportExcel::telecharger(
                'journal_activite_'.now()->format('Ymd_His').'.xlsx',
                "Journal d'activité",
                ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.count($lignes).' entrée(s) ('.number_format(Reglages::get('journal.export_max_lignes'), 0, ',', ' ').' maximum)'],
                ['Date', 'Acteur', 'Module', 'Événement', 'Résumé'],
                $lignes
            );
        }

        return view('journal.journal', [
            'entrees' => $requete->paginate(50)->withQueryString(),
            'modules' => JournalActivite::MODULES,
            'evenements' => JournalActivite::EVENEMENTS,
            'acteurs' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
