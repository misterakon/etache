<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Support\ClotureTache;
use DomainException;
use Illuminate\Http\Request;

/** Validation de la clôture d'une tâche : valider, refuser (motif obligatoire), soumettre de nouveau après un refus. */
class ClotureTacheController extends Controller
{
    private function retour(Tache $tache): string
    {
        return route('feuille_de_route.tache.details', $tache->id).'#validation';
    }

    public function valider(Request $request, $id)
    {
        $tache = Tache::findOrFail($id);
        try {
            ClotureTache::valider($tache, $request->user());
        } catch (DomainException $e) {
            return redirect()->to($this->retour($tache))->withErrors(['cloture' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($tache))->with('success', 'Clôture validée : la tâche est terminée.');
    }

    public function refuser(Request $request, $id)
    {
        $tache = Tache::findOrFail($id);
        $motif = $request->validate(['motif' => ['required', 'string', 'min:5', 'max:2000']], ['motif.required' => 'Le motif du refus est obligatoire.', 'motif.min' => 'Précisez le motif (5 caractères minimum).'])['motif'];
        try {
            ClotureTache::refuser($tache, $request->user(), $motif);
        } catch (DomainException $e) {
            return redirect()->to($this->retour($tache))->withErrors(['cloture' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($tache))->with('success', 'Clôture refusée : le responsable en est informé.');
    }

    public function soumettre(Request $request, $id)
    {
        $tache = Tache::findOrFail($id);
        try {
            ClotureTache::soumettre($tache, $request->user());
        } catch (DomainException $e) {
            return redirect()->to($this->retour($tache))->withErrors(['cloture' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($tache))->with('success', 'Clôture soumise de nouveau à la validation du supérieur hiérarchique.');
    }
}
