<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Support\ClotureExercice;
use App\Support\Reglages;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClotureExerciceController extends Controller
{
    /** Page de clôture et archive : réservées aux profils qui peuvent clôturer ou rouvrir. */
    private function autoriserConsultation(Request $request): void
    {
        $roles = array_merge(Reglages::get('exercice.roles_cloture'), Reglages::get('exercice.roles_reouverture'));
        abort_unless($request->user()?->hasRole($roles), 403, "Vous n'avez pas le droit d'accéder à cette page.");
    }

    public function index(Request $request, $id)
    {
        $this->autoriserConsultation($request);
        $exercice = Exercice::with('cloturePar')->findOrFail($id);

        $controles = $exercice->estClos() ? [] : ClotureExercice::controles($exercice);

        return view('parametre.cloture_exercice', [
            'exercice' => $exercice,
            'controles' => $controles,
            'bloquants' => ClotureExercice::bloquants($controles),
            'peutCloturer' => $request->user()->hasRole(Reglages::get('exercice.roles_cloture')),
            'peutRouvrir' => $request->user()->hasRole(Reglages::get('exercice.roles_reouverture')),
            'archiveDisponible' => $exercice->cloture_archive && Storage::disk(config('pieces_jointes.disque'))->exists($exercice->cloture_archive),
        ]);
    }

    public function cloturer(Request $request, $id)
    {
        abort_unless($request->user()?->hasRole(Reglages::get('exercice.roles_cloture')), 403, "Vous n'avez pas le droit de clôturer un exercice.");
        $exercice = Exercice::findOrFail($id);

        $request->validate(
            ['confirmation' => ['required', 'in:'.$exercice->annee]],
            ['confirmation.required' => "Saisissez l'année de l'exercice pour confirmer.", 'confirmation.in' => "L'année saisie ne correspond pas à l'exercice."]
        );

        try {
            ClotureExercice::cloturer($exercice, $request->user());
        } catch (DomainException $e) {
            return redirect()->route('parametre.exercice.cloture', $exercice->id)->with('error', $e->getMessage())->withErrors(['cloture' => $e->getMessage()]);
        }

        return redirect()->route('parametre.exercice')->with('success', "Exercice {$exercice->annee} clôturé. L'archive Excel est téléchargeable depuis la liste des exercices. Pensez à activer l'exercice suivant.");
    }

    public function rouvrir(Request $request, $id)
    {
        abort_unless($request->user()?->hasRole(Reglages::get('exercice.roles_reouverture')), 403, "Vous n'avez pas le droit de rouvrir un exercice.");
        $exercice = Exercice::findOrFail($id);

        $donnees = $request->validate(
            ['motif' => ['required', 'string', 'min:10', 'max:1000']],
            ['motif.required' => 'Le motif de la réouverture est obligatoire.', 'motif.min' => 'Précisez le motif (10 caractères minimum).']
        );

        try {
            ClotureExercice::rouvrir($exercice, $request->user(), $donnees['motif']);
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withErrors(['cloture' => $e->getMessage()]);
        }

        return redirect()->route('parametre.exercice.cloture', $exercice->id)->with('success', "Exercice {$exercice->annee} rouvert.");
    }

    public function archive(Request $request, $id)
    {
        $this->autoriserConsultation($request);
        $exercice = Exercice::findOrFail($id);
        $disque = Storage::disk(config('pieces_jointes.disque'));

        abort_unless($exercice->cloture_archive && $disque->exists($exercice->cloture_archive), 404, "Aucune archive disponible pour l'exercice {$exercice->annee}.");

        return $disque->download($exercice->cloture_archive, 'archive_exercice_'.$exercice->annee.'.xlsx');
    }
}
