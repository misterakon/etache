<?php

namespace App\Http\Controllers;

use App\Models\Axe;
use App\Models\Direction;
use App\Models\Exercice;
use App\Models\SynthesePilotage;
use App\Support\AnalysesPilotage;
use App\Support\ExportPdf;
use App\Support\ExportPilotage;
use App\Support\GraphiquesSvg;
use App\Support\Journal;
use App\Support\PerimetrePilotage;
use App\Support\RedacteurSynthese;
use App\Support\Reglages;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Pilotage et analyses de la feuille de route : situation, évolution, axes, directions, courrier, synthèse rédigée, exports. ADMIN, DG ; RD pour ses directions. */
class PilotageController extends Controller
{
    private function portee(?string $brut, Exercice $e, ?array $dirs): array
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
    private function contexte(Request $request): array
    {
        $dirs = PerimetrePilotage::directions($request->user());
        $exercices = Exercice::orderByDesc('annee')->get();
        $exercice = $exercices->firstWhere('id', $request->integer('exercice_id')) ?? Exercice::parDefaut() ?? $exercices->first();
        if (! $exercice) {
            return ['exercice' => null, 'exercices' => $exercices, 'dirs' => $dirs];
        }
        $type = array_key_exists((string) $request->input('type'), AnalysesPilotage::TYPES) ? (string) $request->input('type') : 'annee';
        $periode = AnalysesPilotage::periode($type, $request->filled('valeur') ? $request->integer('valeur') : null, $exercice);
        [$porteeType, $porteeId] = $this->portee($request->input('portee'), $exercice, $dirs);

        return [
            'exercice' => $exercice, 'exercices' => $exercices, 'dirs' => $dirs, 'periode' => $periode, 'porteeType' => $porteeType, 'porteeId' => $porteeId,
            'porteeBrut' => $porteeType === 'global' ? 'global' : $porteeType.':'.$porteeId,
        ];
    }

    private function parametres(array $c): array
    {
        return ['exercice_id' => $c['exercice']->id, 'type' => $c['periode']['type'], 'valeur' => $c['periode']['valeur'], 'portee' => $c['porteeBrut']];
    }

    /** Requête des synthèses visibles : un RD ne voit que celles de ses directions ou les siennes. */
    private function syntheses(Request $request, array $c)
    {
        return SynthesePilotage::query()->where('exercice_id', $c['exercice']->id)
            ->when($c['dirs'] !== null, fn ($q) => $q->where(fn ($w) => $w->where('auteur_id', $request->user()->id)->orWhere(fn ($x) => $x->where('portee_type', 'direction')->whereIn('portee_id', $c['dirs'] ?: [0]))));
    }

    private function syntheseCourante(Request $request, array $c): ?SynthesePilotage
    {
        $base = $this->syntheses($request, $c);
        if ($request->filled('synthese_id')) {
            return (clone $base)->find($request->integer('synthese_id'));
        }

        return (clone $base)->where('periode_type', $c['periode']['type'])->where('periode_valeur', $c['periode']['valeur'])
            ->where('portee_type', $c['porteeType'])->where('portee_id', $c['porteeId'])->latest('id')->first();
    }

    private function analyse(array $c): array
    {
        return AnalysesPilotage::calculer($c['exercice'], $c['periode'], $c['porteeType'], $c['porteeId'], $c['dirs']);
    }

    // ------------------------------------------------------------------ ancienne adresse : l'écran est désormais la page « Pilotage »
    public function index(Request $request)
    {
        return redirect()->route('cockpit.index', $request->query());
    }

    // ------------------------------------------------------------------ synthèse
    public function synthetiser(Request $request)
    {
        Gate::authorize('pilotage.synthese');
        $c = $this->contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');

        try {
            $r = RedacteurSynthese::generer($this->analyse($c), $request->user(), $request->boolean('forcer'));
        } catch (\Throwable $e) {
            report($e);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => "La synthèse n'a pas pu être générée : ".$e->getMessage()], 500);
            }
            throw $e;
        }
        $s = $r['synthese']->loadMissing('auteur');

        // Génération dynamique (sans recharger la page) : la page affiche le résultat dès qu'il arrive
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true, 'id' => $s->id, 'html' => RedacteurSynthese::versHtml($s->contenu), 'texte' => $s->contenu,
                'source' => $s->source, 'source_libelle' => $s->libelleSource(), 'couleur' => $s->couleurSource(),
                'pied_gauche' => 'Générée le '.$s->created_at->format('d/m/Y à H:i').($s->auteur ? ' par '.$s->auteur->name : ' automatiquement').($s->modele ? ' · '.$s->modele : ''),
                'pied_droite' => $s->source === 'ia' ? "Rédigée à partir des seuls totaux : aucun nom, courrier ni document n'est transmis." : 'Aucun appel externe.',
                'avertissement' => $r['avertissement'], 'edit_url' => $request->user()->peut('pilotage.corriger_synthese') ? route('pilotage.synthese.editer', $s->id) : null,
            ]);
        }

        $retour = redirect()->route('pilotage.index', $this->parametres($c) + ['synthese_id' => $s->id])->with('success', 'Synthèse générée : '.$s->libelleSource().'.');

        return $r['avertissement'] ? $retour->with('avertissement_pilotage', $r['avertissement']) : $retour;
    }

    /** Correction à la main : une nouvelle version est conservée, l'original reste consultable. */
    public function editer(Request $request, $id)
    {
        Gate::authorize('pilotage.corriger_synthese');
        $base = SynthesePilotage::findOrFail($id);
        $d = $request->validate(['contenu' => ['required', 'string', 'min:20', 'max:12000']], ['contenu.required' => 'La synthèse ne peut pas être vide.', 'contenu.min' => 'La synthèse est trop courte.']);

        $copie = SynthesePilotage::create([
            'exercice_id' => $base->exercice_id, 'periode_type' => $base->periode_type, 'periode_valeur' => $base->periode_valeur, 'portee_type' => $base->portee_type, 'portee_id' => $base->portee_id,
            'contenu' => trim($d['contenu']), 'source' => 'manuel', 'modele' => null, 'empreinte' => $base->empreinte, 'auteur_id' => $request->user()->id,
        ]);
        Journal::noter('pilotage', 'synthese', 'Synthèse du pilotage corrigée à la main (nouvelle version)', $copie);

        return redirect()->route('pilotage.index', ['exercice_id' => $base->exercice_id, 'type' => $base->periode_type, 'valeur' => $base->periode_valeur,
            'portee' => $base->portee_type === 'global' ? 'global' : $base->portee_type.':'.$base->portee_id, 'synthese_id' => $copie->id])->with('success', 'Synthèse enregistrée.');
    }

    public function historique(Request $request)
    {
        $c = $this->contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');
        $lignes = $this->syntheses($request, $c)->with('auteur:id,name')->latest('id')->paginate(20)->withQueryString();

        return view('pilotage.historique', ['lignes' => $lignes, 'exercice' => $c['exercice'], 'exercices' => $c['exercices'], 'axes' => Axe::where('exercice_id', $c['exercice']->id)->pluck('libelle', 'id'), 'directions' => Direction::pluck('nom', 'id')]);
    }

    // ------------------------------------------------------------------ exports
    public function exportExcel(Request $request)
    {
        $c = $this->contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');
        $a = $this->analyse($c);
        Journal::noter('exports', 'export', "Export Excel du pilotage ({$a['portee']} · {$a['periode']['libelle']})", null, null, ['filtres' => $request->query()]);

        return ExportPilotage::excel($a, $this->syntheseCourante($request, $c), $request->user()->name);
    }

    public function exportPdf(Request $request)
    {
        $c = $this->contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');
        $a = $this->analyse($c);
        $g = $this->graphiques($a, $c);
        $synthese = $this->syntheseCourante($request, $c);
        Journal::noter('exports', 'export', "Note de synthèse PDF du pilotage ({$a['portee']} · {$a['periode']['libelle']})", null, null, ['filtres' => $request->query()]);

        $img = fn (string $svg) => $svg === '' ? null : 'data:image/svg+xml;base64,'.base64_encode($svg);

        return ExportPdf::paginer(Pdf::loadView('pilotage.note_pdf', [
            'a' => $a, 'synthese' => $synthese, 'texte' => $synthese ? RedacteurSynthese::versHtml($synthese->contenu) : null,
            'images' => ['courbe' => $img($g['courbe']), 'statuts' => $img($g['statuts'])],
            'barresAxes' => GraphiquesSvg::barresHtml($g['_lignes_axes'], 100), 'barresRetards' => GraphiquesSvg::barresHtml($g['_lignes_retards']),
            'ministere' => Reglages::get('courrier.fiche_ministere'), 'direction' => Reglages::get('courrier.fiche_direction'), 'mention' => Reglages::get('exports.mention_pdf'),
            'editeur' => $request->user()->name,
        ])->setPaper('a4', 'portrait'))->stream('note_synthese_pilotage_'.$a['exercice']->annee.'.pdf');
    }
}
