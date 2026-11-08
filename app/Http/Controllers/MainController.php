<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Models\RoleAffectation;
use App\Models\Tache;
use App\Support\DashboardDonnees;

class MainController extends Controller
{
    public function dashboard()
    {
        // Exercice affiche : celui de l'annee en cours ET actif (meme regle que toutes les pages),
        // sinon l'exercice actif, sinon tout. Pas de champ de selection sur le tableau de bord.
        $exercice = Exercice::parDefaut();
        $user = auth()->user();

        // ADMIN, DG et responsables de direction : tableau de bord complet.
        // Les autres profils (RA...) : tableau de bord personnel, limité aux tâches qui leur sont affectées.
        $vuePersonnelle = ! $user->hasRole('ADMIN|DG|RD');

        $taches = Tache::with([
            'actionMesurable.actionCle.axe',
            'statutExecution',
            'priorite',
            'affectations.personnel',
        ])
            ->when($exercice, fn ($q) => $q->whereHas(
                'actionMesurable.actionCle.axe',
                fn ($a) => $a->where('exercice_id', $exercice->id)
            ))
            ->when($vuePersonnelle, function ($q) use ($user) {
                // Sans agent lié au compte : aucune tâche (filtrer() ignorerait un personnel_id vide et renverrait tout)
                return $user->personnel_id
                    ? $q->filtrer(['personnel_id' => $user->personnel_id])
                    : $q->whereRaw('1 = 0');
            })
            ->get();

        $roleResponsableId = RoleAffectation::where('code', RoleAffectation::RESPONSABLE)->value('id');

        // stats, prioritaires, diligences, performance (calculs : App\Support\DashboardDonnees)
        return view('dashboard', ['exercice' => $exercice, 'vuePersonnelle' => $vuePersonnelle]
            + DashboardDonnees::depuis($taches, $roleResponsableId, $vuePersonnelle));
    }
}
