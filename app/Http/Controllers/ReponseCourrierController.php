<?php

namespace App\Http\Controllers;

use App\Models\Diligence;
use App\Models\ReponseCourrier;
use App\Models\ReponseVisa;
use App\Support\CourrierDroits;
use App\Support\DiligenceDroits;
use App\Support\FichiersCourrier;
use App\Support\Journal;
use App\Support\ReponseWorkflow;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Réponses aux courriers : projet, circuit de visas, signature scannée, départ. */
class ReponseCourrierController extends Controller
{
    private function retour(int $diligenceId): string
    {
        return route('courrier.diligence.show', $diligenceId).'#reponses';
    }

    public function store(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        $user = $request->user();
        abort_unless(DiligenceDroits::gererActions($diligence, $user), 403, 'Vous ne pouvez pas rédiger de réponse pour cette diligence.');
        $retour = $this->retour($diligence->id);
        $reponse = $request->filled('id_') ? ReponseCourrier::where('diligence_id', $diligence->id)->findOrFail($request->input('id_')) : null;

        $validator = Validator::make($request->all(), [
            'objet' => ['required', 'string', 'max:500'],
            'commentaire' => ['nullable', 'string', 'max:2000'],
            'projet' => ['nullable', 'file', 'extensions:'.implode(',', ReponseWorkflow::EXTENSIONS_PROJET), 'max:'.\App\Support\Reglages::get('courrier.scan_taille_max_ko')],
        ], [
            'objet.required' => "Indiquez l'objet de la réponse.",
            'projet.extensions' => 'Format accepté pour le projet : '.strtoupper(implode(', ', ReponseWorkflow::EXTENSIONS_PROJET)).'.',
            'projet.max' => 'Le projet dépasse la taille maximale autorisée (:max Ko).',
            'projet.uploaded' => "Le fichier n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
        ]);
        if ($validator->fails()) {
            return redirect()->to($retour)->withErrors($validator)->withInput();
        }

        try {
            $reponse = ReponseWorkflow::enregistrer($diligence, $reponse, $validator->validated(), $request->file('projet'), $user);
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['objet' => $e->getMessage()])->withInput();
        }

        if ($request->boolean('soumettre')) {
            return $this->soumettre($request, $reponse->id);
        }

        return redirect()->to($retour)->with('success', 'Réponse enregistrée en brouillon.');
    }

    public function soumettre(Request $request, $id)
    {
        $reponse = ReponseCourrier::with('diligence.courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::gererActions($reponse->diligence, $request->user()), 403, 'Vous ne pouvez pas soumettre cette réponse.');

        try {
            ReponseWorkflow::soumettre($reponse, $request->user());
        } catch (DomainException $e) {
            return redirect()->to($this->retour($reponse->diligence_id))->withErrors(['objet' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($reponse->diligence_id))->with('success', 'Réponse soumise au circuit de visa.');
    }

    public function annuler(Request $request, $id)
    {
        $reponse = ReponseCourrier::with('diligence')->findOrFail($id);
        abort_unless(DiligenceDroits::gererActions($reponse->diligence, $request->user()), 403, 'Vous ne pouvez pas annuler cette réponse.');

        try {
            $resultat = ReponseWorkflow::annuler($reponse, $request->user(), $request->input('motif'));
        } catch (DomainException $e) {
            return redirect()->to($this->retour($reponse->diligence_id))->withErrors(['objet' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($reponse->diligence_id))->with('success', $resultat === 'demandee'
            ? "Demande d'annulation envoyée : elle attend l'accord de la personne qui a visé en dernier. Le circuit de visa est suspendu."
            : 'Réponse annulée.');
    }

    /** Le dernier visa (ou l'administrateur, ou le DG) accepte l'annulation demandée. */
    public function accepterAnnulation(Request $request, $id)
    {
        $reponse = ReponseCourrier::findOrFail($id);
        try {
            ReponseWorkflow::trancherAnnulation($reponse, $request->user(), true);
        } catch (DomainException $e) {
            return redirect()->to($this->retour($reponse->diligence_id))->withErrors(['visa' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($reponse->diligence_id))->with('success', 'Annulation acceptée : la réponse est annulée.');
    }

    public function refuserAnnulation(Request $request, $id)
    {
        $reponse = ReponseCourrier::findOrFail($id);
        $motif = $request->validate(['motif' => ['required', 'string', 'min:5', 'max:2000']], ['motif.required' => 'Le motif du refus est obligatoire.', 'motif.min' => 'Précisez le motif (5 caractères minimum).'])['motif'];
        try {
            ReponseWorkflow::trancherAnnulation($reponse, $request->user(), false, $motif);
        } catch (DomainException $e) {
            return redirect()->to($this->retour($reponse->diligence_id))->withErrors(['visa' => $e->getMessage()]);
        }

        return redirect()->to($this->retour($reponse->diligence_id))->with('success', "Annulation refusée : le circuit de visa reprend.");
    }

    public function viser(Request $request, $id)
    {
        $visa = ReponseVisa::with('reponse')->findOrFail($id);
        $retour = $this->retour($visa->reponse->diligence_id);
        $donnees = $request->validate(['commentaire' => ['nullable', 'string', 'max:2000']]);

        try {
            ReponseWorkflow::viser($visa, $request->user(), $donnees['commentaire'] ?? null);
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['visa' => $e->getMessage()]);
        }

        return redirect()->to($retour)->with('success', 'Visa enregistré : la réponse passe à l\'étape suivante.');
    }

    public function refuser(Request $request, $id)
    {
        $visa = ReponseVisa::with('reponse')->findOrFail($id);
        $retour = $this->retour($visa->reponse->diligence_id);
        $motif = $request->validate(['commentaire' => ['required', 'string', 'min:5', 'max:2000']], ['commentaire.required' => 'Le motif du refus est obligatoire.', 'commentaire.min' => 'Précisez le motif (5 caractères minimum).'])['commentaire'];

        try {
            ReponseWorkflow::refuser($visa, $request->user(), $motif);
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['visa' => $e->getMessage()]);
        }

        return redirect()->to($retour)->with('success', 'Réponse refusée : elle retourne au responsable pour correction.');
    }

    public function signer(Request $request, $id)
    {
        $visa = ReponseVisa::with('reponse')->findOrFail($id);
        $retour = $this->retour($visa->reponse->diligence_id);
        $request->validate([
            'signature' => ['required', 'file', 'extensions:'.implode(',', ReponseWorkflow::EXTENSIONS_SIGNATURE), 'max:'.\App\Support\Reglages::get('courrier.scan_taille_max_ko')],
        ], [
            'signature.required' => 'Déposez la réponse signée (scan).',
            'signature.extensions' => 'Format accepté : '.strtoupper(implode(', ', ReponseWorkflow::EXTENSIONS_SIGNATURE)).'.',
            'signature.max' => 'Le scan dépasse la taille maximale autorisée (:max Ko).',
            'signature.uploaded' => "Le fichier n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
        ]);

        try {
            $departs = ReponseWorkflow::signer($visa, $request->user(), $request->file('signature'));
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['visa' => $e->getMessage()]);
        }

        $message = 'Réponse signée.'.($departs ? ' Courrier départ '.$departs[0]->numero.' enregistré.' : '');

        return redirect()->to($retour)->with('success', $message);
    }

    /** Projet ou réponse signée : aperçu dans le navigateur ou téléchargement (?telecharger=1). */
    public function fichier(Request $request, $id, string $categorie)
    {
        $reponse = ReponseCourrier::with(['diligence.courrier', 'fichiers'])->findOrFail($id);
        abort_unless(DiligenceDroits::voir($reponse->diligence, $request->user()), 403, "Vous n'avez pas accès à cette réponse.");
        // Réponse à un courrier confidentiel : réservée aux personnes qui peuvent voir ce courrier (imputation, circuit de visa, ADMIN, DG)
        abort_if($reponse->diligence->courrier->estConfidentiel() && ! CourrierDroits::peutVoir($reponse->diligence->courrier, $request->user()), 403, 'Cette réponse concerne un courrier confidentiel : accès réservé aux personnes désignées.');

        $piece = $categorie === 'signature' ? $reponse->signee() : $reponse->projet();
        abort_unless($piece, 404, 'Aucun fichier disponible.');
        if ($reponse->diligence->courrier->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', 'Fichier de réponse du courrier confidentiel '.$reponse->diligence->courrier->numero.' consulté', $piece, $reponse->diligence->courrier);
        }

        return FichiersCourrier::servir($piece, 'reponse_'.$reponse->diligence->courrier->numero.($categorie === 'signature' ? '_signee' : '_projet'), ! $request->boolean('telecharger'));
    }
}
