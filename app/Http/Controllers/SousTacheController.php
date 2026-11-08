<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Notifications\SousTacheNotification;
use App\Support\NotifSousTache;
use App\Models\SousTache;
use App\Models\Tache;
use App\Support\AvancementTache;
use App\Support\Journal;
use App\Support\Reglages;
use App\Support\SousTacheDroits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SousTacheController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_' => ['nullable', 'integer', 'exists:sous_taches,id'],
            'tache_id' => ['required', 'exists:taches,id'],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'echeance' => ['nullable', 'date'],
            'personnel_id' => ['nullable', 'integer'],
            'modalites' => ['nullable', 'string', 'max:2000'],
            'conditions' => ['nullable', 'string', 'max:2000'],
        ], ['libelle.required' => 'Le libellé est obligatoire.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $tache = Tache::findOrFail($donnees['tache_id']);
        $retour = route('feuille_de_route.tache.details', $tache->id).'#sous-taches'; // revient sur l'onglet

        $droits = SousTacheDroits::pour($tache, auth()->user());
        if (! $droits['gerer']) {
            return redirect()->to($retour)->withErrors(['droit' => $droits['raison']]);
        }

        $agent = ! empty($donnees['personnel_id']) ? Personnel::where('actif', true)->find($donnees['personnel_id']) : null;
        if (! empty($donnees['personnel_id']) && ! $agent) {
            return redirect()->to($retour)->withErrors(['personnel_id' => 'Agent introuvable ou inactif.']);
        }
        // Validée d'office si l'auteur est le validateur ; sinon en attente du supérieur hiérarchique
        $valideur = SousTacheDroits::peutValider($tache, auth()->user());
        $validation = $valideur
            ? ['statut_validation' => 'validee', 'valide_par' => auth()->id(), 'valide_le' => now(), 'motif_refus' => null]
            : ['statut_validation' => 'a_valider', 'valide_par' => null, 'valide_le' => null, 'motif_refus' => null];
        $notifier = false;

        $id = $donnees['id_'] ?? null;

        if ($id) {
            $sous = SousTache::where('tache_id', $tache->id)->findOrFail($id);
            $ancien = $sous->libelle;
            $sous->fill([
                'libelle' => $donnees['libelle'],
                'description' => $donnees['description'] ?? null,
                'echeance' => $donnees['echeance'] ?? null,
                'personnel_id' => $agent?->id,
                'modalites' => $donnees['modalites'] ?? null,
                'conditions' => $donnees['conditions'] ?? null,
            ]);
            if ($sous->isDirty(['libelle', 'echeance', 'personnel_id', 'modalites', 'conditions'])) {
                $sous->fill($validation); // tout changement de fond repasse par la validation
            }
            $sous->save();
            $notifier = $sous->wasChanged(['libelle', 'echeance', 'personnel_id', 'modalites', 'conditions', 'statut_validation']);
            if ($ancien !== $sous->libelle) {
                Journal::noter('sous_taches', 'modification', "Sous-tâche modifiée : « {$ancien} » → « {$sous->libelle} »", $sous, $tache, ['avant' => $ancien, 'apres' => $sous->libelle]);
            }
            $message = 'Sous-tâche modifiée avec succès !';
        } else {
            $max = Reglages::get('sous_taches.max_par_tache');
            if ($tache->sousTaches()->count() >= $max) {
                return redirect()->to($retour)->withErrors(['libelle' => 'Maximum de '.$max.' sous-tâches atteint.']);
            }
            $sous = SousTache::create([
                'tache_id' => $tache->id,
                'libelle' => $donnees['libelle'],
                'description' => $donnees['description'] ?? null,
                'echeance' => $donnees['echeance'] ?? null,
                'ordre' => ((int) $tache->sousTaches()->max('ordre')) + 1,
                'cree_par' => auth()->id(),
                'personnel_id' => $agent?->id,
                'modalites' => $donnees['modalites'] ?? null,
                'conditions' => $donnees['conditions'] ?? null,
            ] + $validation);
            Journal::noter('sous_taches', 'creation', 'Sous-tâche ajoutée : '.$sous->libelle, $sous, $tache);
            $notifier = true;
            $message = $valideur ? 'Sous-tâche ajoutée avec succès !' : 'Sous-tâche ajoutée : en attente de validation par votre supérieur hiérarchique.';
        }

        // Cloche : le validateur est alerté d'une sous-tâche « à valider » ; l'agent, quand une sous-tâche validée lui est confiée
        if ($notifier) {
            if ($sous->statut_validation === 'a_valider') {
                SousTacheDroits::validateurs($tache)->each(fn ($p) => NotifSousTache::versPersonnel($p->id, $sous, SousTacheNotification::A_VALIDER, null, auth()->id()));
            } elseif ($agent && (int) $agent->id !== (int) auth()->user()->personnel_id) {
                NotifSousTache::versPersonnel($agent->id, $sous, SousTacheNotification::CONFIEE, null, auth()->id());
            }
        }

        // Un ajout dilue le pourcentage : l'avancement est recalculé
        AvancementTache::recalculer($tache, auth()->id());

        return redirect()->to($retour)->with('success', $message);
    }

    public function get($id)
    {
        return response()->json(SousTache::findOrFail($id));
    }

    public function cocher(Request $request, $id)
    {
        $sous = SousTache::findOrFail($id);
        $tache = $sous->tache;
        if (! SousTacheDroits::peutCocher($sous, auth()->user())) {
            return response()->json(['status' => false, 'message' => "Cette sous-tâche ne peut pas être cochée : elle n'est pas validée, ou vous n'en êtes ni l'agent ni le responsable."], 403);
        }

        $coche = $request->boolean('cochee');
        $etaitEnAttente = $tache->cloture_validation === 'a_valider';
        if ($sous->cochee !== $coche) {
            $sous->update([
                'cochee' => $coche,
                'cochee_le' => $coche ? now() : null,
                'cochee_par' => $coche ? auth()->id() : null,
            ]);
            Journal::noter('sous_taches', $coche ? 'coche' : 'decoche', ($coche ? 'Sous-tâche faite : ' : 'Sous-tâche remise à faire : ').$sous->libelle, $sous, $tache);
            AvancementTache::recalculer($tache, auth()->id());
        }

        return $this->reponse($tache, '', ! $etaitEnAttente && $tache->fresh()->cloture_validation === 'a_valider');
    }

    public function valider($id)
    {
        return $this->decider($id, true, null);
    }

    public function rejeter(Request $request, $id)
    {
        $motif = $request->validate(['motif' => ['required', 'string', 'min:5', 'max:2000']], ['motif.required' => 'Le motif du refus est obligatoire.'])['motif'];

        return $this->decider($id, false, $motif);
    }

    private function decider($id, bool $accepte, ?string $motif): JsonResponse
    {
        $sous = SousTache::findOrFail($id);
        $tache = $sous->tache;
        if (! SousTacheDroits::peutValider($tache, auth()->user())) {
            return response()->json(['status' => false, 'message' => 'Seul le supérieur hiérarchique du responsable peut valider cette sous-tâche.'], 403);
        }
        if ($sous->statut_validation !== 'a_valider') {
            return response()->json(['status' => false, 'message' => "Cette sous-tâche n'est pas en attente de validation."], 422);
        }
        $sous->update(['statut_validation' => $accepte ? 'validee' : 'refusee', 'valide_par' => auth()->id(), 'valide_le' => now(), 'motif_refus' => $accepte ? null : $motif]);
        Journal::noter('sous_taches', $accepte ? 'validation' : 'refus', ($accepte ? 'Sous-tâche validée : ' : 'Sous-tâche refusée : ').$sous->libelle.($motif ? ' — '.$motif : ''), $sous, $tache);
        AvancementTache::recalculer($tache, auth()->id());

        if ($accepte) {
            NotifSousTache::versUtilisateur($sous->cree_par, $sous, SousTacheNotification::VALIDEE, null, auth()->id());
            if ($sous->personnel_id) {
                NotifSousTache::versPersonnel($sous->personnel_id, $sous, SousTacheNotification::CONFIEE, null, auth()->id());
            }
        } else {
            NotifSousTache::versUtilisateur($sous->cree_par, $sous, SousTacheNotification::REFUSEE, $motif, auth()->id());
        }

        return $this->reponse($tache, $accepte ? 'Sous-tâche validée.' : 'Sous-tâche refusée.');
    }

    public function destroy($id)
    {
        $sous = SousTache::findOrFail($id);
        $tache = $sous->tache;
        if ($refus = $this->refuser($tache)) {
            return $refus;
        }

        Journal::noter('sous_taches', 'suppression', 'Sous-tâche supprimée : '.$sous->libelle.($sous->cochee ? ' (elle était faite)' : ''), $sous, $tache);
        $sous->delete();
        AvancementTache::recalculer($tache, auth()->id());

        return $this->reponse($tache, 'Sous-tâche supprimée.');
    }

    private function refuser(Tache $tache): ?JsonResponse
    {
        $droits = SousTacheDroits::pour($tache, auth()->user());

        return $droits['gerer'] ? null : response()->json(['status' => false, 'message' => $droits['raison']], 403);
    }

    private function reponse(Tache $tache, string $message = '', bool $clotureSoumise = false): JsonResponse
    {
        $tache->refresh()->load('statutExecution');

        return response()->json([
            'status' => true,
            'message' => $message,
            'avancement' => (int) $tache->avancement_pourcentage,
            'statut' => $tache->statutExecution?->libelle,
            'cloture_soumise' => $clotureSoumise,
            'html' => view('feuille_de_route._sous_taches_liste', [
                'tache' => $tache,
                'droits' => SousTacheDroits::pour($tache, auth()->user()),
            ])->render(),
        ]);
    }
}
