<?php

namespace App\Http\Controllers;

use App\Models\Diligence;
use App\Models\DiligenceDelai;
use App\Notifications\DiligenceNotification;
use App\Support\DiligenceDroits;
use App\Support\Journal;
use App\Support\NotifDiligence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Demandes de délai : le responsable demande un report, celui qui a imputé (ou le niveau supérieur) l'accorde ou le refuse. */
class DiligenceDelaiController extends Controller
{
    private function retour(int $diligenceId): string
    {
        return route('courrier.diligence.show', $diligenceId).'#delais';
    }

    public function demander(Request $request, $id)
    {
        $diligence = Diligence::with(['courrier', 'imputation', 'diligenceParente'])->findOrFail($id);
        abort_unless(DiligenceDroits::peutDemanderDelai($diligence, $request->user()), 403, "Vous ne pouvez pas demander de délai pour cette diligence (une demande est peut-être déjà en attente).");
        $retour = $this->retour($diligence->id);

        $validator = Validator::make($request->all(), [
            'echeance_souhaitee' => ['required', 'date', 'after:'.$diligence->echeance->toDateString()],
            'motif' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'echeance_souhaitee.required' => 'Indiquez la nouvelle échéance souhaitée.',
            'echeance_souhaitee.after' => "La nouvelle échéance doit être postérieure à l'échéance actuelle (".$diligence->echeance->format('d/m/Y').').',
            'motif.required' => 'Expliquez pourquoi un délai supplémentaire est nécessaire.',
            'motif.min' => 'Précisez le motif (10 caractères minimum).',
        ]);
        if ($validator->fails()) {
            return redirect()->to($retour)->withErrors($validator)->withInput();
        }
        $d = $validator->validated();
        $souhaitee = Carbon::parse($d['echeance_souhaitee'])->startOfDay();

        if ($diligence->diligenceParente && $souhaitee->gt($diligence->diligenceParente->echeance)) {
            return redirect()->to($retour)->withErrors(['echeance_souhaitee' => "Cette date dépasse l'échéance de la diligence parente (".$diligence->diligenceParente->echeance->format('d/m/Y').") : le report doit d'abord être obtenu auprès du niveau supérieur."])->withInput();
        }

        $delai = DiligenceDelai::create([
            'diligence_id' => $diligence->id, 'demande_par' => $request->user()->id, 'echeance_actuelle' => $diligence->echeance,
            'echeance_souhaitee' => $souhaitee, 'motif' => trim($d['motif']), 'statut' => 'en_attente',
        ]);
        Journal::noter('courrier', 'delai', "Délai demandé pour la diligence {$diligence->code} : échéance {$diligence->echeance->format('d/m/Y')} → {$souhaitee->format('d/m/Y')}", $delai, $diligence->courrier, ['motif' => $delai->motif]);

        $detail = "Nouvelle échéance souhaitée : {$souhaitee->format('d/m/Y')}.";
        NotifDiligence::versUtilisateur($diligence->imputation?->imputee_par, $diligence, DiligenceNotification::DELAI_DEMANDE, $detail, $request->user()->id);
        if ($diligence->diligenceParente) {
            NotifDiligence::versPersonnel($diligence->diligenceParente->responsable_personnel_id, $diligence, DiligenceNotification::DELAI_DEMANDE, $detail, $request->user()->id);
        }

        return redirect()->to($retour)->with('success', 'Demande de délai transmise.');
    }

    public function decider(Request $request, $id)
    {
        $delai = DiligenceDelai::with(['diligence.courrier', 'diligence.imputation', 'diligence.diligenceParente'])->findOrFail($id);
        $diligence = $delai->diligence;
        $user = $request->user();
        abort_unless(DiligenceDroits::peutDeciderDelai($diligence, $user), 403, "Vous ne pouvez pas décider de cette demande de délai.");
        $retour = $this->retour($diligence->id);

        if ($delai->statut !== 'en_attente') {
            return redirect()->to($retour)->withErrors(['decision' => 'Cette demande a déjà été traitée.']);
        }

        if ($request->input('decision') === 'accorder') {
            $validator = Validator::make($request->all(), ['echeance_accordee' => ['nullable', 'date', 'after_or_equal:today']], ['echeance_accordee.after_or_equal' => "La nouvelle échéance ne peut pas être dans le passé."]);
            if ($validator->fails()) {
                return redirect()->to($retour)->withErrors($validator);
            }
            $accordee = Carbon::parse($validator->validated()['echeance_accordee'] ?? $delai->echeance_souhaitee)->startOfDay();
            if ($diligence->diligenceParente && ! $user->hasRole('ADMIN|DG') && $accordee->gt($diligence->diligenceParente->echeance)) {
                return redirect()->to($retour)->withErrors(['echeance_accordee' => "Dépasse l'échéance de la diligence parente (".$diligence->diligenceParente->echeance->format('d/m/Y').')']);
            }

            $delai->update(['statut' => 'accorde', 'decide_par' => $user->id, 'decide_le' => now(), 'echeance_accordee' => $accordee]);
            $diligence->update(['echeance' => $accordee]);
            Journal::noter('courrier', 'delai', "Délai accordé pour la diligence {$diligence->code} : nouvelle échéance {$accordee->format('d/m/Y')}", $delai, $diligence->courrier);
            NotifDiligence::versUtilisateur($delai->demande_par, $diligence, DiligenceNotification::DELAI_ACCORDE, "Nouvelle échéance : {$accordee->format('d/m/Y')}.", $user->id);

            return redirect()->to($retour)->with('success', "Délai accordé : nouvelle échéance au {$accordee->format('d/m/Y')}.");
        }

        $motif = $request->validate(['motif_decision' => ['required', 'string', 'min:5', 'max:2000']], ['motif_decision.required' => 'Le motif du refus est obligatoire.', 'motif_decision.min' => 'Précisez le motif (5 caractères minimum).'])['motif_decision'];
        $delai->update(['statut' => 'refuse', 'decide_par' => $user->id, 'decide_le' => now(), 'motif_decision' => $motif]);
        Journal::noter('courrier', 'delai', "Délai refusé pour la diligence {$diligence->code} : {$motif}", $delai, $diligence->courrier);
        NotifDiligence::versUtilisateur($delai->demande_par, $diligence, DiligenceNotification::DELAI_REFUSE, $motif, $user->id);

        return redirect()->to($retour)->with('success', 'Demande de délai refusée.');
    }
}
