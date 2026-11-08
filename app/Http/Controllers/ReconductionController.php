<?php

namespace App\Http\Controllers;

use App\Models\ActionMesurable;
use App\Models\Exercice;
use App\Models\Tache;
use App\Support\ReconductionExercice;
use App\Support\Reglages;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;

class ReconductionController extends Controller
{
    private function autoriser(Request $request): void
    {
        abort_unless($request->user()?->hasRole(Reglages::get('exercice.roles_reconduction')), 403, "Vous n'avez pas le droit de reconduire des tâches.");
    }

    public function index(Request $request)
    {
        $this->autoriser($request);

        $exercices = Exercice::orderByDesc('annee')->get();
        $source = $exercices->firstWhere('id', $request->integer('source_id'));
        $destination = $exercices->firstWhere('id', $request->integer('destination_id'));

        $erreur = null;
        $taches = collect();
        $axesSource = collect();
        $destinationsAM = collect();
        $presel = $prefill = [];

        if ($source && $destination) {
            if ($source->id === $destination->id) {
                $erreur = "L'exercice source et l'exercice de destination doivent être différents.";
            } elseif ($destination->estClos()) {
                $erreur = "L'exercice {$destination->annee} est clos : on ne peut pas y reconduire de tâches.";
            } else {
                $taches = Tache::dansExercice($source->id)->nonTerminees()
                    ->with(['actionMesurable.actionCle.axe', 'statutExecution'])
                    ->orderBy('code')->get();

                $axesSource = $source->axes()->with('actionsCles.actionsMesurables')->orderBy('ordre')->get();

                // Actions mesurables de l'exercice de destination, dans l'ordre de la feuille de route
                $destinationsAM = ActionMesurable::whereHas('actionCle.axe', fn ($q) => $q->where('exercice_id', $destination->id))
                    ->with('actionCle.axe')->get()
                    ->sortBy(fn ($am) => sprintf('%05d-%05d-%05d-%s', $am->actionCle->axe->ordre, $am->actionCle->ordre, $am->ordre, $am->code))
                    ->values();

                // Pré-sélection : action mesurable de même chemin (axe > action clé > action mesurable) dans la destination
                $cle = fn ($axe, $ac, $am) => mb_strtolower(trim($axe).'|'.trim($ac).'|'.trim($am));
                $correspondances = $destinationsAM->mapWithKeys(fn ($am) => [$cle($am->actionCle->axe->libelle, $am->actionCle->libelle, $am->libelle) => $am->id]);

                // Échéance pré-remplie : l'échéance actuelle, tant qu'elle n'est pas antérieure au début du nouvel exercice
                $debut = $destination->date_debut ?? Carbon::create($destination->annee, 1, 1);
                $preremplir = Reglages::get('exercice.reconduction_echeance_preremplie');

                foreach ($taches as $t) {
                    $am = $t->actionMesurable;
                    $presel[$t->id] = $correspondances[$cle($am->actionCle->axe->libelle, $am->actionCle->libelle, $am->libelle)] ?? null;
                    $prefill[$t->id] = $preremplir && $t->date_fin && $t->date_fin->gte($debut) ? $t->date_fin->toDateString() : '';
                }
            }
        }

        return view('parametre.reconduction', [
            'exercices' => $exercices, 'source' => $source, 'destination' => $destination, 'erreur' => $erreur,
            'taches' => $taches, 'axesSource' => $axesSource, 'destinationsAM' => $destinationsAM,
            'presel' => $presel, 'prefill' => $prefill,
            'creerStructure' => Reglages::get('exercice.reconduction_structure_si_absente'),
            'optAffectations' => Reglages::get('exercice.reconduction_affectations'),
            'optSousTaches' => Reglages::get('exercice.reconduction_sous_taches'),
        ]);
    }

    /** Reconduction de tâches non terminées. */
    public function executer(Request $request)
    {
        $this->autoriser($request);

        $donnees = $request->validate([
            'source_id' => ['required', 'exists:exercices,id'],
            'destination_id' => ['required', 'exists:exercices,id', 'different:source_id'],
            'taches' => ['required', 'array', 'min:1'],
            'taches.*' => ['integer', 'exists:taches,id'],
            'echeances' => ['required', 'array'],
            'destinations' => ['nullable', 'array'],
            'destinations.*' => ['nullable', 'integer'],
        ], [
            'taches.required' => 'Sélectionnez au moins une tâche à reconduire.',
            'destination_id.different' => "L'exercice source et l'exercice de destination doivent être différents.",
        ]);

        $source = Exercice::findOrFail($donnees['source_id']);
        $destination = Exercice::findOrFail($donnees['destination_id']);
        $retour = route('parametre.reconduction', ['source_id' => $source->id, 'destination_id' => $destination->id]);

        if ($destination->estClos()) {
            return redirect()->to($retour)->withErrors(['destination_id' => "L'exercice {$destination->annee} est clos."]);
        }

        // Échéance obligatoire pour chaque tâche reprise
        $echeances = $destinations = [];
        foreach ($donnees['taches'] as $id) {
            $date = $donnees['echeances'][$id] ?? null;
            if (! $date || ! strtotime($date)) {
                return redirect()->to($retour)->withErrors(['echeances' => "Renseignez l'échéance de chaque tâche reconduite."])->withInput();
            }
            $echeances[(int) $id] = date('Y-m-d', strtotime($date));
            $destinations[(int) $id] = ((int) ($donnees['destinations'][$id] ?? 0)) ?: null;
        }

        try {
            $stats = ReconductionExercice::reconduire(
                $source, $destination, $echeances, $destinations,
                $request->boolean('reprendre_affectations'), $request->boolean('reprendre_sous_taches'),
                Reglages::get('exercice.reconduction_structure_si_absente'), $request->user()->id
            );
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['echeances' => $e->getMessage()])->withInput();
        }

        return redirect()->to($retour)->with('success',
            "{$stats['taches']} tâche(s) reconduite(s) vers {$destination->annee} (créés : {$stats['axes']} axe(s), {$stats['actions_cles']} action(s) clé(s), {$stats['actions_mesurables']} action(s) mesurable(s), {$stats['sous_taches']} sous-tâche(s)).");
    }

    /** Reconduction de la structure seule (axes, actions clés, actions mesurables). */
    public function executerStructure(Request $request)
    {
        $this->autoriser($request);

        $donnees = $request->validate([
            'source_id' => ['required', 'exists:exercices,id'],
            'destination_id' => ['required', 'exists:exercices,id', 'different:source_id'],
            'axes' => ['nullable', 'array'], 'axes.*' => ['integer'],
            'actions_cles' => ['nullable', 'array'], 'actions_cles.*' => ['integer'],
            'actions_mesurables' => ['nullable', 'array'], 'actions_mesurables.*' => ['integer'],
        ], ['destination_id.different' => "L'exercice source et l'exercice de destination doivent être différents."]);

        $source = Exercice::findOrFail($donnees['source_id']);
        $destination = Exercice::findOrFail($donnees['destination_id']);
        $retour = route('parametre.reconduction', ['source_id' => $source->id, 'destination_id' => $destination->id]);

        if ($destination->estClos()) {
            return redirect()->to($retour)->withErrors(['destination_id' => "L'exercice {$destination->annee} est clos."]);
        }
        if (empty($donnees['axes']) && empty($donnees['actions_cles']) && empty($donnees['actions_mesurables'])) {
            return redirect()->to($retour)->withErrors(['structure' => 'Sélectionnez au moins un axe, une action clé ou une action mesurable.']);
        }

        $stats = ReconductionExercice::reconduireStructure(
            $source, $destination,
            $donnees['axes'] ?? [], $donnees['actions_cles'] ?? [], $donnees['actions_mesurables'] ?? [],
            $request->boolean('reprendre_affectations'), $request->user()->id
        );

        return redirect()->to($retour)->with('success',
            "Structure reconduite vers {$destination->annee} (créés : {$stats['axes']} axe(s), {$stats['actions_cles']} action(s) clé(s), {$stats['actions_mesurables']} action(s) mesurable(s) ; ceux qui existaient déjà n'ont pas été dupliqués).");
    }
}
