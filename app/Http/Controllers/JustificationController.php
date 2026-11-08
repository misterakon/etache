<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\TacheJustification;
use App\Support\JournalTache;
use App\Support\JustificationRetard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class JustificationController extends Controller
{
    private function erreur(string $message, int $code = 422)
    {
        return response()->json(['status' => false, 'message' => $message], $code);
    }

    /** Page "Demandes a valider" : ADMIN, DG, RD. */
    public function index(Request $request)
    {
        $user = auth()->user();
        Gate::authorize('justification.voir');

        // Filtre venant du tableau de bord : ?statut=en_attente | approuvee | refusee | annulee
        $statut = in_array($request->query('statut'), [
            TacheJustification::EN_ATTENTE, TacheJustification::APPROUVEE,
            TacheJustification::REFUSEE, TacheJustification::ANNULEE,
        ], true) ? $request->query('statut') : null;

        $relations = ['tache.statutExecution', 'demandeur.personnel', 'decideur.personnel'];

        $aValider = TacheJustification::enAttente()->aDecider($user)->with($relations)->oldest()->get();

        // Demandes traitées : approuvées et refusées ; le filtre ?statut= peut aussi demander les annulées
        $statutsTraites = in_array($statut, [TacheJustification::APPROUVEE, TacheJustification::REFUSEE, TacheJustification::ANNULEE], true)
            ? [$statut]
            : [TacheJustification::APPROUVEE, TacheJustification::REFUSEE];

        $traitees = TacheJustification::visiblePar($user)
            ->whereIn('statut', $statutsTraites)
            ->with($relations)->latest('decide_le')->limit(50)->get();

        return view('feuille_de_route.justification', [
            'aValider' => $aValider,
            'traitees' => $traitees,
            'motifs' => JustificationRetard::MOTIFS,
            'filtre' => $statut, // null, ou le statut demandé depuis le tableau de bord
        ]);
    }

    /**
     * Demande de justification ou de report (AJAX). Responsable/pilote de la tache ou administrateur.
     */
    public function store(Request $request, $id)
    {
        $tache = Tache::with('statutExecution')->findOrFail($id);
        $user = auth()->user();

        if (! JustificationRetard::peutDemander($user, $tache)) {
            return $this->erreur('Seuls le responsable, le pilote de la tâche ou un administrateur peuvent faire cette demande.', 403);
        }
        if (! JustificationRetard::tacheOuverte($tache)) {
            return $this->erreur('Cette tâche est terminée ou annulée.');
        }
        if (JustificationRetard::enAttente($tache)) {
            return $this->erreur('Une demande est déjà en attente pour cette tâche.');
        }

        $donnees = $request->validate([
            'type' => ['required', Rule::in([TacheJustification::TYPE_JUSTIFICATION, TacheJustification::TYPE_REPORT])],
            'motif' => ['required', Rule::in(array_keys(JustificationRetard::MOTIFS))],
            'commentaire' => ['required', 'string', 'min:10', 'max:2000'],
            'echeance_proposee' => ['required_if:type,'.TacheJustification::TYPE_REPORT, 'nullable', 'date'],
        ], [
            'commentaire.required' => 'Le commentaire est obligatoire.',
            'commentaire.min' => 'Le commentaire doit contenir au moins 10 caractères.',
            'echeance_proposee.required_if' => 'Indiquez la nouvelle échéance proposée.',
        ]);

        $jours = 0;
        $proposee = null;
        if ($donnees['type'] === TacheJustification::TYPE_REPORT) {
            if (! $tache->date_fin) {
                return $this->erreur("Cette tâche n'a pas d'échéance : demandez plutôt une modification.");
            }
            $proposee = Carbon::parse($donnees['echeance_proposee'])->startOfDay();
            if ($proposee->lte($tache->date_fin)) {
                return $this->erreur("La nouvelle échéance doit être postérieure à l'échéance actuelle ({$tache->date_fin->format('d/m/Y')}).");
            }
            $jours = (int) $tache->date_fin->diffInDays($proposee);
        }

        [$niveau, $decideurId] = JustificationRetard::decideurPour(
            $user, $donnees['type'], $jours, JustificationRetard::nbReports($tache)
        );

        $justification = TacheJustification::create([
            'tache_id' => $tache->id,
            'demandeur_id' => $user->id,
            'type' => $donnees['type'],
            'motif' => $donnees['motif'],
            'commentaire' => $donnees['commentaire'],
            'echeance_actuelle' => $tache->date_fin,
            'echeance_proposee' => $proposee,
            'statut' => TacheJustification::EN_ATTENTE,
            'niveau_decision' => $niveau,
            'decideur_personnel_id' => $decideurId,
        ]);

        JustificationRetard::notifierDemande($justification);

        $qui = $niveau === TacheJustification::NIVEAU_RD ? 'au responsable de direction' : 'au Directeur Général';

        return response()->json(['status' => true, 'message' => "Demande transmise {$qui}."]);
    }

    /**
     * Decision (AJAX) : decision = approuver | refuser. Le refus exige un motif.
     * Une acceptation de report change l'echeance, journalisee avec le decideur comme auteur.
     */
    public function decision(Request $request, $id)
    {
        $justification = TacheJustification::with('tache.statutExecution')->findOrFail($id);
        $user = auth()->user();

        if ($justification->statut !== TacheJustification::EN_ATTENTE) {
            return $this->erreur('Cette demande a déjà été traitée.');
        }
        if (! JustificationRetard::peutDecider($user, $justification)) {
            return $this->erreur("Vous n'avez pas le droit de décider cette demande.", 403);
        }

        $donnees = $request->validate([
            'decision' => ['required', Rule::in(['approuver', 'refuser'])],
            'commentaire' => [Rule::requiredIf($request->input('decision') === 'refuser'), 'nullable', 'string', 'max:2000'],
        ], [
            'commentaire.required' => 'Un refus doit être motivé.',
        ]);

        $approuve = $donnees['decision'] === 'approuver';
        $tache = $justification->tache;

        if ($approuve && $justification->estReport()) {
            if (! JustificationRetard::tacheOuverte($tache)) {
                return $this->erreur('La tâche est désormais terminée ou annulée : refusez la demande.');
            }
            if ($tache->date_fin && $justification->echeance_proposee->lte($tache->date_fin)) {
                return $this->erreur("L'échéance de la tâche a changé depuis la demande : la date proposée n'est plus un report.");
            }
        }

        DB::transaction(function () use ($justification, $tache, $approuve, $donnees, $user) {
            if ($approuve && $justification->estReport()) {
                $avant = JournalTache::instantane($tache);
                $tache->update(['date_fin' => $justification->echeance_proposee, 'derniere_mise_a_jour_le' => now()]);
                JournalTache::enregistrer($tache, $avant, $user->id); // auteur = decideur
            }

            $justification->update([
                'statut' => $approuve ? TacheJustification::APPROUVEE : TacheJustification::REFUSEE,
                'decide_par' => $user->id,
                'decision_commentaire' => $donnees['commentaire'] ?? null,
                'decide_le' => now(),
            ]);
        });

        JustificationRetard::notifierDecision($justification);

        return response()->json(['status' => true, 'message' => $approuve ? 'Demande approuvée.' : 'Demande refusée.']);
    }

    /** Le demandeur (ou un administrateur) retire une demande encore en attente. */
    public function annuler($id)
    {
        $justification = TacheJustification::findOrFail($id);
        $user = auth()->user();

        if ($justification->statut !== TacheJustification::EN_ATTENTE) {
            return $this->erreur('Cette demande a déjà été traitée.');
        }
        if ($justification->demandeur_id !== $user->id && ! $user->hasRole('ADMIN')) {
            return $this->erreur('Vous ne pouvez annuler que vos propres demandes.', 403);
        }

        $justification->update(['statut' => TacheJustification::ANNULEE, 'decide_le' => now()]);

        return response()->json(['status' => true, 'message' => 'Demande annulée.']);
    }
}
