<?php

namespace App\Http\Controllers;

use App\Models\Fonction;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Support\AffectationPoste;
use App\Support\Journal;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffectationController extends Controller
{
    /** Affecte un agent à un poste (formulaire modal de la liste du personnel et de la fiche). Réservé à l'administrateur. */
    public function store(Request $request)
    {
        abort_unless($request->user()?->hasRole('ADMIN'), 403, "Seul l'administrateur peut affecter un agent à un poste.");

        $validator = Validator::make($request->all(), [
            'personnel_id' => ['required', 'exists:personnel,id'],
            'fonction_id' => ['required', 'exists:fonctions,id'],
            'unite_id' => ['nullable', 'integer'],
            'interim' => ['nullable', 'boolean'],
            'date_debut' => ['required', 'date'],
            'reference_acte' => ['nullable', 'string', 'max:100'],
            'confirmer' => ['nullable', 'boolean'],
        ], [
            'fonction_id.required' => 'Choisissez la fonction.',
            'date_debut.required' => 'La date de prise de fonction est obligatoire.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $d = $validator->validated();

        $agent = Personnel::findOrFail($d['personnel_id']);
        $fonction = Fonction::where('actif', true)->findOrFail($d['fonction_id']);

        try {
            $resultat = AffectationPoste::affecter(
                $agent, $fonction, $d['unite_id'] ?? null, $request->boolean('interim') && $fonction->estMandat(),
                Carbon::parse($d['date_debut']), $d['reference_acte'] ?? null, $request->boolean('confirmer'), $request->user()->id
            );
        } catch (DomainException $e) {
            return redirect()->back()->withErrors(['affectation' => $e->getMessage()])->withInput();
        }

        if ($resultat['statut'] === 'conflit') {
            return redirect()->back()->withInput()->with('affectation_conflit', $resultat['message']);
        }

        return redirect()->back()->with('success', $resultat['message']);
    }

    /**
     * Met fin à une affectation en cours (intérim ou titulaire) : date de fin et motif obligatoires, historique conservé.
     * Fin d'un intérim : le titulaire reprend le poste. Fin d'un titulaire : le poste devient vacant et la fonction de l'agent est vidée.
     */
    public function fin(Request $request)
    {
        abort_unless($request->user()?->hasRole('ADMIN'), 403, "Seul l'administrateur peut mettre fin à une affectation.");

        $mandat = MandatResponsable::with(['personnel', 'direction', 'sousDirection', 'service'])->findOrFail($request->input('mandat_id'));
        if ($mandat->date_fin && $mandat->date_fin->lt(today())) {
            return redirect()->back()->withErrors(['fin_affectation' => 'Cette affectation est déjà terminée.']);
        }

        $validator = Validator::make($request->all(), [
            'date_fin' => ['required', 'date', 'after_or_equal:'.$mandat->date_debut->toDateString()],
            'motif_fin' => ['required', 'string', 'max:255'],
        ], [
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin ne peut pas précéder la prise de fonction ('.$mandat->date_debut->format('d/m/Y').').',
            'motif_fin.required' => 'Indiquez le motif de la fin.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $d = $validator->validated();

        // La date saisie est le DERNIER JOUR AU POSTE (inclus) : l'agent reste en poste jusqu'à la fin de cette journée.
        $dernierJour = Carbon::parse($d['date_fin'])->startOfDay();

        // Évite le doublon (double clic, formulaire renvoyé) : une même fin ne s'enregistre pas deux fois dans l'historique
        if ($mandat->date_fin && $mandat->date_fin->copy()->startOfDay()->equalTo($dernierJour)) {
            return redirect()->back()->withErrors(['fin_affectation' => 'Cette affectation se termine déjà le '.$dernierJour->format('d/m/Y').' : aucun changement.']);
        }
        $mandat->update(['date_fin' => $dernierJour, 'motif_fin' => $d['motif_fin']]);

        $agent = $mandat->personnel;
        $interim = $mandat->type === MandatResponsable::INTERIM;
        $libelle = $mandat->libellePoste();

        if ($agent && $dernierJour->lte(today())) {
            AffectationPoste::synchroniserFonction($agent, $dernierJour->copy()->addDay());
        }
        Journal::noter('personnel', 'fin_affectation', 'Fin '.($interim ? "de l'intérim" : 'de la fonction').' de '.($agent?->nomComplet() ?? '—')." : {$libelle} (dernier jour au poste : ".$dernierJour->format('d/m/Y').") — {$d['motif_fin']}", $agent, $agent, [
            'mandat' => $mandat->id, 'type' => $mandat->type, 'dernier_jour' => $dernierJour->toDateString(), 'motif' => $d['motif_fin'],
        ]);

        return redirect()->back()->with('success', ($interim ? "L'intérim" : 'La fonction')." de {$agent?->nomComplet()} se termine à la fin de la journée du ".$dernierJour->format('d/m/Y').'.'.($interim ? ' Le titulaire du poste reprend la main.' : ' Le poste est vacant.'));
    }
}
