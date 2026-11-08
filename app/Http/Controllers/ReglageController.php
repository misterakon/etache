<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use App\Support\Reglages;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReglageController extends Controller
{
    private function autoriser(Request $request, string $droit): void
    {
        abort_unless($request->user()?->peut($droit), 403, "Vous n'avez pas accès à cette action des réglages.");
    }

    public function index(Request $request)
    {
        $this->autoriser($request, 'reglages.voir');

        $catalogue = Reglages::catalogue();
        $valeurs = $modifies = [];
        foreach ($catalogue as $module => $m) {
            foreach (array_keys($m['reglages']) as $reglage) {
                $cle = "{$module}.{$reglage}";
                $valeurs[$cle] = Reglages::get($cle);
                $modifies[$cle] = ! Reglages::estParDefaut($cle);
            }
        }

        return view('reglages.reglages', [
            'catalogue' => $catalogue,
            'valeurs' => $valeurs,
            'modifies' => $modifies,
            'historique' => JournalActivite::where('module', 'reglages')->orderByDesc('created_at')->orderByDesc('id')->limit(30)->get(),
        ]);
    }

    /** Enregistre tous les réglages d'un module (formulaire d'un module). */
    public function store(Request $request)
    {
        $this->autoriser($request, 'reglages.modifier');

        $module = (string) $request->input('module');
        $catalogue = Reglages::catalogue();
        abort_unless(isset($catalogue[$module]), 404);

        $erreurs = $nouvelles = [];
        foreach ($catalogue[$module]['reglages'] as $reglage => $definition) {
            if (Reglages::verrouille("{$module}.{$reglage}")) {
                continue; // réglage verrouillé : sa valeur ne peut plus changer
            }
            $brut = match ($definition['type']) {
                'bool' => $request->boolean("valeurs.{$reglage}"),
                'liste' => $request->input("valeurs.{$reglage}", []),
                default => $request->input("valeurs.{$reglage}"),
            };

            [$valeur, $erreur] = Reglages::normaliser($definition, $brut);
            if ($erreur) {
                $erreurs["valeurs.{$reglage}"] = $definition['libelle'].' : '.$erreur;
            } else {
                $nouvelles["{$module}.{$reglage}"] = $valeur;
            }
        }

        if ($erreurs) {
            return redirect()->back()->withErrors($erreurs)->withInput();
        }

        $nb = 0;
        foreach ($nouvelles as $cle => $valeur) {
            $nb += Reglages::enregistrer($cle, $valeur) ? 1 : 0;
        }

        return redirect()->route('reglages.index', ['module' => $module])
            ->with('success', $nb ? "{$nb} réglage(s) enregistré(s)." : 'Aucune modification.');
    }

    public function retablir(Request $request)
    {
        $this->autoriser($request, 'reglages.retablir');

        $module = (string) $request->input('module');
        if ($request->input('cle') === null || $request->input('cle') === '') {
            return $this->retablirModule($module);
        }

        $cle = (string) $request->input('cle');
        abort_unless(Reglages::definition($cle), 404);

        try {
            Reglages::retablir($cle);
        } catch (DomainException $e) {
            return redirect()->back()->withErrors(['reglage' => $e->getMessage()]);
        }

        return redirect()->route('reglages.index', ['module' => explode('.', $cle)[0]])->with('success', 'Valeur par défaut rétablie.');
    }

    /** Rétablit la valeur par défaut de tous les réglages modifiés d'une rubrique (les réglages verrouillés sont laissés tels quels). */
    private function retablirModule(string $module): RedirectResponse
    {
        $catalogue = Reglages::catalogue();
        abort_unless(isset($catalogue[$module]), 404);

        $nb = 0;
        foreach (array_keys($catalogue[$module]['reglages']) as $reglage) {
            $cle = "{$module}.{$reglage}";
            if (Reglages::estParDefaut($cle)) {
                continue;
            }

            try {
                Reglages::retablir($cle);
                $nb++;
            } catch (DomainException) {
                // réglage verrouillé : sa valeur ne peut plus changer
            }
        }

        return redirect()->route('reglages.index', ['module' => $module])
            ->with('success', $nb ? "{$nb} réglage(s) rétabli(s) à leur valeur par défaut." : 'Aucun réglage à rétablir.');
    }
}
