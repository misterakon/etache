<?php

namespace App\Http\Controllers;

use App\Models\Diligence;
use App\Models\DiligenceAction;
use App\Notifications\DiligenceNotification;
use App\Support\DiligenceDroits;
use App\Support\Journal;
use App\Support\NotifDiligence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Plan de traitement d'une diligence : actions à réaliser, validées par le supérieur hiérarchique du responsable. */
class DiligenceActionController extends Controller
{
    private const MAX_ACTIONS = 50;

    private function retour(int $diligenceId): string
    {
        return route('courrier.diligence.show', $diligenceId).'#traitement';
    }

    public function store(Request $request, $id)
    {
        $diligence = Diligence::with('courrier', 'responsable')->findOrFail($id);
        $user = $request->user();
        abort_unless(DiligenceDroits::gererActions($diligence, $user), 403, "Vous ne pouvez pas gérer le plan de traitement de cette diligence.");
        $retour = $this->retour($diligence->id);

        $validator = Validator::make($request->all(), [
            'id_' => ['nullable', 'integer'],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'personnel_id' => ['nullable', 'integer'],
            'echeance' => ['nullable', 'date', 'before_or_equal:'.$diligence->echeance->toDateString()],
            'modalites' => ['nullable', 'string', 'max:2000'],
            'conditions' => ['nullable', 'string', 'max:2000'],
        ], [
            'libelle.required' => "Indiquez l'action à mener.",
            'echeance.before_or_equal' => "L'échéance de l'action ne peut pas dépasser celle de la diligence (".$diligence->echeance->format('d/m/Y').').',
        ]);
        if ($validator->fails()) {
            return redirect()->to($retour)->withErrors($validator)->withInput();
        }
        $d = $validator->validated();

        $valideur = DiligenceDroits::peutValiderAction($diligence, $user);
        $validation = $valideur
            ? ['statut_validation' => 'validee', 'valide_par' => $user->id, 'valide_le' => now(), 'motif_refus' => null]
            : ['statut_validation' => 'a_valider', 'valide_par' => null, 'valide_le' => null, 'motif_refus' => null];
        $champs = [
            'libelle' => trim($d['libelle']), 'description' => $d['description'] ?? null, 'personnel_id' => null,
            'echeance' => $d['echeance'] ?? null, 'modalites' => $d['modalites'] ?? null, 'conditions' => $d['conditions'] ?? null,
        ];

        if (! empty($d['id_'])) {
            $action = DiligenceAction::where('diligence_id', $diligence->id)->findOrFail($d['id_']);
            $action->fill($champs);
            if ($action->isDirty(['libelle', 'personnel_id', 'echeance', 'modalites', 'conditions'])) {
                $action->fill($validation); // tout changement de fond repasse par la validation
            }
            $action->save();
            Journal::noter('courrier', 'traitement', "Action modifiée sur la diligence {$diligence->code} : {$action->libelle}", $action, $diligence->courrier);
        } else {
            if ($diligence->actions()->count() >= self::MAX_ACTIONS) {
                return redirect()->to($retour)->withErrors(['libelle' => 'Maximum de '.self::MAX_ACTIONS.' actions atteint.'])->withInput();
            }
            $action = DiligenceAction::create($champs + $validation + [
                'diligence_id' => $diligence->id, 'ordre' => ((int) $diligence->actions()->max('ordre')) + 1, 'cree_par' => $user->id,
            ]);
            Journal::noter('courrier', 'traitement', "Action ajoutée sur la diligence {$diligence->code} : {$action->libelle}", $action, $diligence->courrier);
        }

        if ($action->statut_validation === 'a_valider') {
            NotifDiligence::versPersonnel(DiligenceDroits::validateur($diligence)?->id, $diligence, DiligenceNotification::ACTION_A_VALIDER, $action->libelle, $user->id);
        }

        return redirect()->to($retour)->with('success', $valideur ? 'Action enregistrée.' : 'Action enregistrée : elle attend la validation de votre supérieur hiérarchique.');
    }

    public function cocher(Request $request, $id)
    {
        $action = DiligenceAction::with('diligence.courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::peutCocherAction($action, $request->user()), 403, "Cette action ne peut pas être cochée (non validée, ou vous n'êtes ni son agent ni le responsable).");

        $faite = $request->boolean('faite');
        if ($action->faite !== $faite) {
            $action->update(['faite' => $faite, 'faite_le' => $faite ? now() : null, 'faite_par' => $faite ? $request->user()->id : null]);
            Journal::noter('courrier', 'traitement', ($faite ? 'Action faite : ' : 'Action remise à faire : ').$action->libelle." (diligence {$action->diligence->code})", $action, $action->diligence->courrier);
        }

        return redirect()->to($this->retour($action->diligence_id));
    }

    public function valider(Request $request, $id)
    {
        return $this->decider($request, $id, true);
    }

    public function rejeter(Request $request, $id)
    {
        return $this->decider($request, $id, false);
    }

    private function decider(Request $request, $id, bool $accepte)
    {
        $action = DiligenceAction::with('diligence.courrier', 'diligence.responsable')->findOrFail($id);
        $diligence = $action->diligence;
        $user = $request->user();
        abort_unless(DiligenceDroits::peutValiderAction($diligence, $user), 403, "Seul le supérieur hiérarchique du responsable peut valider cette action.");
        $retour = $this->retour($diligence->id);

        if ($action->statut_validation !== 'a_valider') {
            return redirect()->to($retour)->withErrors(['libelle' => "Cette action n'est pas en attente de validation."]);
        }
        $motif = null;
        if (! $accepte) {
            $motif = $request->validate(['motif' => ['required', 'string', 'min:5', 'max:2000']], ['motif.required' => 'Le motif du refus est obligatoire.', 'motif.min' => 'Précisez le motif (5 caractères minimum).'])['motif'];
        }

        $action->update(['statut_validation' => $accepte ? 'validee' : 'refusee', 'valide_par' => $user->id, 'valide_le' => now(), 'motif_refus' => $motif]);
        Journal::noter('courrier', 'traitement', ($accepte ? 'Action validée : ' : 'Action refusée : ').$action->libelle.($motif ? " — {$motif}" : '')." (diligence {$diligence->code})", $action, $diligence->courrier);

        if ($accepte) {
            NotifDiligence::versUtilisateur($action->cree_par, $diligence, DiligenceNotification::ACTION_VALIDEE, $action->libelle, $user->id);
        } else {
            NotifDiligence::versUtilisateur($action->cree_par, $diligence, DiligenceNotification::ACTION_REFUSEE, "« {$action->libelle} » — {$motif}", $user->id);
        }

        return redirect()->to($retour)->with('success', $accepte ? 'Action validée.' : 'Action refusée : le responsable en est informé.');
    }

    public function destroy(Request $request, $id)
    {
        $action = DiligenceAction::with('diligence.courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::gererActions($action->diligence, $request->user()), 403, 'Vous ne pouvez pas supprimer cette action.');

        Journal::noter('courrier', 'traitement', "Action supprimée : {$action->libelle} (diligence {$action->diligence->code})".($action->faite ? ' — elle était faite' : ''), $action, $action->diligence->courrier);
        $action->delete();

        return redirect()->to($this->retour($action->diligence_id))->with('success', 'Action supprimée.');
    }
}
