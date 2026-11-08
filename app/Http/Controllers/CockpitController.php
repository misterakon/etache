<?php

namespace App\Http\Controllers;

use App\Models\Axe;
use App\Models\Direction;
use App\Support\Cockpit;
use App\Support\InstantanesPilotage;
use App\Support\Reglages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Page « Pilotage » : une seule page pour le DG, l'administrateur et les RD (limités à leurs directions).
 * Le cadre (filtres, indicateurs, onglets) est affiché une fois ; chaque onglet est ensuite chargé en AJAX.
 */
class CockpitController extends Controller
{
    public function index(Request $request): View
    {
        $c = Cockpit::contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');
        InstantanesPilotage::capturerSiNecessaire();
        $a = Cockpit::analyse($c);
        $onglet = array_key_exists((string) $request->input('onglet'), Cockpit::ONGLETS) ? (string) $request->input('onglet') : 'ensemble';
        $syntheseId = $request->integer('synthese_id') ?: null;
        if ($syntheseId) {
            $onglet = 'synthese';
        }

        return view('cockpit.index', [
            'c' => $c, 'kpis' => Cockpit::kpis($a), 'onglet' => $onglet, 'parametres' => Cockpit::parametres($c),
            'directions' => $this->directions($c), 'axes' => Axe::where('exercice_id', $c['exercice']->id)->orderBy('code')->get(['id', 'code', 'libelle']),
            'restreint' => $c['dirs'] !== null, 'syntheseId' => $syntheseId,
        ]);
    }

    public function onglet(Request $request, string $onglet): JsonResponse
    {
        abort_unless(array_key_exists($onglet, Cockpit::ONGLETS), 404);
        $c = Cockpit::contexte($request);
        abort_unless($c['exercice'], 404, 'Aucun exercice.');
        $a = Cockpit::analyse($c);
        $user = $request->user();
        $p = Cockpit::parametres($c);

        $donnees = match ($onglet) {
            'ensemble' => Cockpit::ensemble($c, $a, $user),
            'taches' => Cockpit::listeTaches($c, (string) $request->input('filtre', 'retards'), $user),
            'courrier' => Cockpit::courrier($a),
            'structure' => Cockpit::structure($a),
            'agents' => Cockpit::agents(),
            'evolution' => Cockpit::evolution($c, $a),
            'synthese' => [
                'synthese' => Cockpit::syntheseCourante($c, $user, $request->integer('synthese_id') ?: null), 'historique' => Cockpit::historiqueSyntheses($c, $user),
                'iaReglee' => (bool) Reglages::get('synthese.ia_active'),
            ],
            'exports' => ['filtres' => Cockpit::FILTRES_TACHES, 'types' => Cockpit::TYPE_EXPORT],
        };

        $html = view('cockpit.onglets.'.$onglet, ['d' => $donnees, 'c' => $c, 'a' => $a, 'p' => $p, 'peutEditer' => $user->peut('pilotage.corriger_synthese')])->render();

        return response()->json([
            'html' => $html, 'kpis' => Cockpit::kpis($a), 'graphiques' => $this->graphiques($onglet, $donnees),
        ]);
    }

    /** @return array<string, mixed> données JSON pour les graphiques de l'onglet (ApexCharts côté navigateur) */
    private function graphiques(string $onglet, array $d): array
    {
        return match ($onglet) {
            'ensemble' => ['repartition' => $d['repartition'], 'total' => $d['total'], 'courbe' => $d['courbe'], 'axes' => $d['axes']],
            'courrier' => $d['graph'] ?? [],
            'structure' => $d['graph'],
            'agents' => ['charge' => $d['graph']],
            'evolution' => $d['graph'],
            default => [],
        };
    }

    private function directions(array $c)
    {
        return Direction::query()->when($c['dirs'] !== null, fn ($q) => $q->whereIn('id', $c['dirs'] ?: [0]))->orderBy('nom')->get(['id', 'nom']);
    }
}
