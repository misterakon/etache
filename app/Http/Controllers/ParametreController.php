<?php

namespace App\Http\Controllers;

use App\Models\CanalNotification;
use App\Models\Emploi;
use App\Models\Exercice;
use App\Models\FamilleEmploi;
use App\Models\Priorite;
use App\Models\ProfilAcces;
use App\Models\RegleAlerte;
use App\Models\RoleAffectation;
use App\Models\SituationTemporelle;
use App\Models\StatutExecution;
use App\Models\TypeAgent;
use App\Support\AvancementTache;
use App\Support\Droits;
use App\Support\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ParametreController extends Controller
{
    public function exercices()
    {
        $exercices = Exercice::orderByDesc('annee')->get();

        return view('parametre.exercice', compact('exercices'));
    }

    public function storeExercice(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'exercice.modifier' : 'exercice.creer');

        $validator = Validator::make($request->all(), [
            'annee' => [
                'required',
                'digits:4',
                'integer',
                $id ? "unique:exercices,annee,{$id}" : 'unique:exercices,annee',
            ],
            'libelle' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'actif' => ['required', 'boolean'],
            'mode_avancement' => ['required', 'in:manuel,sous_taches'],
        ], [
            'annee.unique' => 'Cette année d\'exercice existe déjà.',
            'annee.digits' => 'L\'année doit contenir exactement 4 chiffres.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $donnees['libelle'] = $donnees['libelle'] ?: "Feuille de route {$donnees['annee']}";

        if ($id) {
            $exercice = Exercice::findOrFail($id);

            if ($exercice->estClos() && $exercice->mode_avancement !== $donnees['mode_avancement']) {
                $refus = "Exercice clos : le mode d'avancement ne peut pas être modifié.";

                return redirect()->back()->with('error', $refus)->withErrors(['exercice_clos' => $refus]);
            }

            $ancienMode = $exercice->mode_avancement;
            $exercice->update($donnees);
            $message = 'Exercice mis à jour avec succès !';

            if ($ancienMode !== $exercice->mode_avancement) {
                Journal::noter('exercices', 'changement_mode_avancement',
                    "Exercice {$exercice->annee} : mode d'avancement « {$ancienMode} » → « {$exercice->mode_avancement} »",
                    $exercice, null, ['avant' => $ancienMode, 'apres' => $exercice->mode_avancement]);

                if ($exercice->avancementParSousTaches()) {
                    $nb = AvancementTache::recalculerExercice($exercice, auth()->id());
                    $message .= " {$nb} tâche(s) recalculée(s) à partir des sous-tâches.";
                }
            }
        } else {
            Exercice::create($donnees);
            $message = 'Exercice créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function apercuModeExercice(Request $request, $id)
    {
        $exercice = Exercice::findOrFail($id);
        $mode = $request->query('mode');

        if ($mode === $exercice->mode_avancement) {
            return response()->json(['change' => false]);
        }
        if ($mode === Exercice::MODE_SOUS_TACHES) {
            $a = AvancementTache::apercu($exercice);

            return response()->json(['change' => true, 'message' => "Passage en mode <b>par sous-tâches</b> : {$a['changent']} tâche(s) sur {$a['total']} seront recalculées, "
                ."dont {$a['vers_zero']} passeront à 0 %.<br>Les anciennes valeurs restent dans l'historique de chaque tâche."]);
        }

        return response()->json(['change' => true, 'message' => 'Retour au mode <b>manuel</b> : les avancements actuels sont conservés et redeviennent modifiables.']);
    }

    public function getExercice($id)
    {
        $exercice = Exercice::findOrFail($id);

        return response()->json($exercice);
    }

    public function deleteExercice($id)
    {
        Gate::authorize('exercice.supprimer');
        $exercice = Exercice::find($id);

        if (! $exercice) {
            return response()->json(['status' => false, 'message' => 'Exercice introuvable.'], 404);
        }

        if ($exercice->estClos()) {
            return response()->json(['status' => false, 'message' => "Exercice clos : suppression impossible. Rouvrez-le d'abord."], 423);
        }

        $exercice->delete();

        return response()->json(['status' => true, 'message' => 'Exercice supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Type agent
    |--------------------------------------------------------------------------
    */

    public function typeAgents()
    {
        $typeAgents = TypeAgent::orderBy('libelle')->get();

        return view('parametre.type_agent', compact('typeAgents'));
    }

    public function storeTypeAgent(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'type_agent.modifier' : 'type_agent.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:types_agent,libelle,{$id}" : 'unique:types_agent,libelle',
            ],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $typeAgent = TypeAgent::findOrFail($id);
            $typeAgent->update($donnees);
            $message = 'Type d\'agent mis à jour avec succès !';
        } else {
            TypeAgent::create($donnees);
            $message = 'Type d\'agent créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getTypeAgent($id)
    {
        $typeAgent = TypeAgent::findOrFail($id);

        return response()->json($typeAgent);
    }

    public function deleteTypeAgent($id)
    {
        Gate::authorize('type_agent.supprimer');
        $typeAgent = TypeAgent::find($id);

        if (! $typeAgent) {
            return response()->json(['status' => false, 'message' => 'Type d\'agent introuvable.'], 404);
        }

        $typeAgent->delete();

        return response()->json(['status' => true, 'message' => 'Type d\'agent supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Famille d'emploi
    |--------------------------------------------------------------------------
    */

    public function familleEmplois()
    {
        $famillesEmploi = FamilleEmploi::orderBy('libelle')->get();

        return view('parametre.famille_emploi', compact('famillesEmploi'));
    }

    public function storeFamilleEmploi(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'famille_emploi.modifier' : 'famille_emploi.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:familles_emploi,libelle,{$id}" : 'unique:familles_emploi,libelle',
            ],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $famille = FamilleEmploi::findOrFail($id);
            $famille->update($donnees);
            $message = 'Famille d\'emploi mise à jour avec succès !';
        } else {
            FamilleEmploi::create($donnees);
            $message = 'Famille d\'emploi créée avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getFamilleEmploi($id)
    {
        $famille = FamilleEmploi::findOrFail($id);

        return response()->json($famille);
    }

    public function deleteFamilleEmploi($id)
    {
        Gate::authorize('famille_emploi.supprimer');
        $famille = FamilleEmploi::find($id);

        if (! $famille) {
            return response()->json(['status' => false, 'message' => 'Famille d\'emploi introuvable.'], 404);
        }

        $famille->delete();

        return response()->json(['status' => true, 'message' => 'Famille d\'emploi supprimée avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Emploi
    |--------------------------------------------------------------------------
    */

    public function emplois()
    {
        $emplois = Emploi::with('familleEmploi')->orderBy('libelle')->get();
        $famillesEmploi = FamilleEmploi::where('actif', true)->orderBy('libelle')->get();

        return view('parametre.emploi', compact('emplois', 'famillesEmploi'));
    }

    public function storeEmploi(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'emploi.modifier' : 'emploi.creer');

        $validator = Validator::make($request->all(), [
            'famille_emploi_id' => ['required', 'exists:familles_emploi,id'],
            'libelle' => ['required', 'string', 'max:255'],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $emploi = Emploi::findOrFail($id);
            $emploi->update($donnees);
            $message = 'Emploi mis à jour avec succès !';
        } else {
            Emploi::create($donnees);
            $message = 'Emploi créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getEmploi($id)
    {
        $emploi = Emploi::findOrFail($id);

        return response()->json($emploi);
    }

    public function deleteEmploi($id)
    {
        Gate::authorize('emploi.supprimer');
        $emploi = Emploi::find($id);

        if (! $emploi) {
            return response()->json(['status' => false, 'message' => 'Emploi introuvable.'], 404);
        }

        $emploi->delete();

        return response()->json(['status' => true, 'message' => 'Emploi supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Profil d'acces
    |--------------------------------------------------------------------------
    */

    public function profilsAcces()
    {
        $profilsAcces = ProfilAcces::withCount('users')->orderByDesc('systeme')->orderBy('libelle')->get();

        return view('parametre.profil_acces', compact('profilsAcces'));
    }

    public function storeProfilAcces(Request $request)
    {
        $id = $request->input('id_');
        $profil = $id ? ProfilAcces::findOrFail($id) : null;
        Gate::authorize($id ? 'profils_acces.modifier' : 'profils_acces.creer');

        // Le profil système (Admin) a tous les droits d'office : il n'est ni modifiable, ni supprimable,
        // pour garantir qu'il reste toujours au moins un compte capable de tout administrer.
        abort_if($profil?->systeme, 403, "Le profil Admin n'est pas modifiable.");

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:profils_acces,libelle,{$id}" : 'unique:profils_acces,libelle',
            ],
            'slug' => [
                'required', 'string', 'max:20',
                $id ? "unique:profils_acces,slug,{$id}" : 'unique:profils_acces,slug',
            ],
            'actif' => ['required', 'boolean'],
            'niveaux' => ['nullable', 'array'],
            'niveaux.*' => ['string', Rule::in(array_keys(Droits::NIVEAUX))],
            'speciales' => ['nullable', 'array'],
            'speciales.*' => ['string', Rule::in(Droits::tous())],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $donnees['slug'] = mb_strtoupper($donnees['slug']);

        // Chaque niveau choisi doit exister pour son sous-menu (ex. un sous-menu sans niveau « Gestion »)
        $pages = Droits::pages();
        foreach ($donnees['niveaux'] ?? [] as $page => $niveau) {
            if (! isset($pages[$page]) || ($niveau !== 'aucun' && ! isset($pages[$page]['niveaux'][$niveau]))) {
                return redirect()->back()->withErrors(['niveaux' => 'Niveau d\'accès invalide pour « '.($pages[$page]['libelle'] ?? $page).' ».'])->withInput();
            }
        }

        $donnees['droits'] = Droits::depuisChoix($donnees['niveaux'] ?? [], $donnees['speciales'] ?? []);
        unset($donnees['niveaux'], $donnees['speciales']);

        if ($profil) {
            $avant = ['libelle' => $profil->libelle, 'slug' => $profil->slug, 'droits' => $profil->droits];
            $profil->update($donnees);
            Journal::noter('parametre', 'profil_acces_modifie', 'Profil d\'accès « '.$profil->libelle.' » modifié.', $profil, null, ['avant' => $avant, 'apres' => $donnees]);
            $message = 'Profil d\'accès mis à jour avec succès !';
        } else {
            $profil = ProfilAcces::create($donnees);
            Journal::noter('parametre', 'profil_acces_cree', 'Profil d\'accès « '.$profil->libelle.' » créé.', $profil);
            $message = 'Profil d\'accès créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getProfilAcces($id)
    {
        $profil = ProfilAcces::findOrFail($id);

        return response()->json([
            'id' => $profil->id,
            'libelle' => $profil->libelle,
            'slug' => $profil->slug,
            'actif' => $profil->actif,
            'systeme' => $profil->systeme,
        ] + Droits::versChoix($profil->droits ?? []));
    }

    public function deleteProfilAcces($id)
    {
        Gate::authorize('profils_acces.supprimer');
        $profil = ProfilAcces::find($id);

        if (! $profil) {
            return response()->json(['status' => false, 'message' => 'Profil d\'accès introuvable.'], 404);
        }

        if ($profil->systeme) {
            return response()->json(['status' => false, 'message' => 'Le profil Admin ne peut pas être supprimé.'], 422);
        }
        if ($profil->users()->exists()) {
            return response()->json(['status' => false, 'message' => "Ce profil est attribué à des comptes : changez d'abord leur profil."], 422);
        }

        Journal::noter('parametre', 'profil_acces_supprime', 'Profil d\'accès « '.$profil->libelle.' » supprimé.', $profil);
        $profil->delete();

        return response()->json(['status' => true, 'message' => 'Profil d\'accès supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Role d'affectation
    |--------------------------------------------------------------------------
    */

    public function rolesAffectation()
    {
        $rolesAffectation = RoleAffectation::orderBy('libelle')->get();

        return view('parametre.role_affectation', compact('rolesAffectation'));
    }

    public function storeRoleAffectation(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'role_affectation.modifier' : 'role_affectation.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:roles_affectation,libelle,{$id}" : 'unique:roles_affectation,libelle',
            ],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $role = RoleAffectation::findOrFail($id);
            $role->update($donnees);
            $message = 'Rôle d\'affectation mis à jour avec succès !';
        } else {
            RoleAffectation::create($donnees);
            $message = 'Rôle d\'affectation créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getRoleAffectation($id)
    {
        $role = RoleAffectation::findOrFail($id);

        return response()->json($role);
    }

    public function deleteRoleAffectation($id)
    {
        Gate::authorize('role_affectation.supprimer');
        $role = RoleAffectation::find($id);

        if (! $role) {
            return response()->json(['status' => false, 'message' => 'Rôle d\'affectation introuvable.'], 404);
        }

        $role->delete();

        return response()->json(['status' => true, 'message' => 'Rôle d\'affectation supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Priorite
    |--------------------------------------------------------------------------
    */

    public function priorites()
    {
        $priorites = Priorite::orderBy('libelle')->get();

        return view('parametre.priorite', compact('priorites'));
    }

    public function storePriorite(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'priorite.modifier' : 'priorite.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:priorites,libelle,{$id}" : 'unique:priorites,libelle',
            ],
            'couleur' => ['nullable', 'string', 'max:20'],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $priorite = Priorite::findOrFail($id);
            $priorite->update($donnees);
            $message = 'Priorité mise à jour avec succès !';
        } else {
            Priorite::create($donnees);
            $message = 'Priorité créée avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getPriorite($id)
    {
        $priorite = Priorite::findOrFail($id);

        return response()->json($priorite);
    }

    public function deletePriorite($id)
    {
        Gate::authorize('priorite.supprimer');
        $priorite = Priorite::find($id);

        if (! $priorite) {
            return response()->json(['status' => false, 'message' => 'Priorité introuvable.'], 404);
        }

        $priorite->delete();

        return response()->json(['status' => true, 'message' => 'Priorité supprimée avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Statut d'execution
    |--------------------------------------------------------------------------
    */

    public function statutsExecution()
    {
        $statutsExecution = StatutExecution::orderBy('libelle')->get();

        return view('parametre.statut_execution', compact('statutsExecution'));
    }

    public function storeStatutExecution(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'statut_execution.modifier' : 'statut_execution.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:statuts_execution,libelle,{$id}" : 'unique:statuts_execution,libelle',
            ],
            'couleur' => ['nullable', 'string', 'max:20'],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $statut = StatutExecution::findOrFail($id);
            $statut->update($donnees);
            $message = 'Statut d\'exécution mis à jour avec succès !';
        } else {
            StatutExecution::create($donnees);
            $message = 'Statut d\'exécution créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getStatutExecution($id)
    {
        $statut = StatutExecution::findOrFail($id);

        return response()->json($statut);
    }

    public function deleteStatutExecution($id)
    {
        Gate::authorize('statut_execution.supprimer');
        $statut = StatutExecution::find($id);

        if (! $statut) {
            return response()->json(['status' => false, 'message' => 'Statut d\'exécution introuvable.'], 404);
        }

        $statut->delete();

        return response()->json(['status' => true, 'message' => 'Statut d\'exécution supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Situation temporelle
    |--------------------------------------------------------------------------
    */

    public function situationsTemporelles()
    {
        $situationsTemporelles = SituationTemporelle::orderBy('libelle')->get();

        return view('parametre.situation_temporelle', compact('situationsTemporelles'));
    }

    public function storeSituationTemporelle(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'situation_temporelle.modifier' : 'situation_temporelle.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:situations_temporelles,libelle,{$id}" : 'unique:situations_temporelles,libelle',
            ],
            'couleur' => ['nullable', 'string', 'max:20'],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $situation = SituationTemporelle::findOrFail($id);
            $situation->update($donnees);
            $message = 'Situation temporelle mise à jour avec succès !';
        } else {
            SituationTemporelle::create($donnees);
            $message = 'Situation temporelle créée avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getSituationTemporelle($id)
    {
        $situation = SituationTemporelle::findOrFail($id);

        return response()->json($situation);
    }

    public function deleteSituationTemporelle($id)
    {
        Gate::authorize('situation_temporelle.supprimer');
        $situation = SituationTemporelle::find($id);

        if (! $situation) {
            return response()->json(['status' => false, 'message' => 'Situation temporelle introuvable.'], 404);
        }

        $situation->delete();

        return response()->json(['status' => true, 'message' => 'Situation temporelle supprimée avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Canal de notification
    |--------------------------------------------------------------------------
    */

    public function canauxNotification()
    {
        $canauxNotification = CanalNotification::orderBy('libelle')->get();

        return view('parametre.canal_notification', compact('canauxNotification'));
    }

    public function storeCanalNotification(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'canal_notification.modifier' : 'canal_notification.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255',
                $id ? "unique:canaux_notification,libelle,{$id}" : 'unique:canaux_notification,libelle',
            ],
            'actif' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $canal = CanalNotification::findOrFail($id);
            $canal->update($donnees);
            $message = 'Canal de notification mis à jour avec succès !';
        } else {
            CanalNotification::create($donnees);
            $message = 'Canal de notification créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getCanalNotification($id)
    {
        $canal = CanalNotification::findOrFail($id);

        return response()->json($canal);
    }

    public function deleteCanalNotification($id)
    {
        Gate::authorize('canal_notification.supprimer');
        $canal = CanalNotification::find($id);

        if (! $canal) {
            return response()->json(['status' => false, 'message' => 'Canal de notification introuvable.'], 404);
        }

        $canal->delete();

        return response()->json(['status' => true, 'message' => 'Canal de notification supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Regle d'alerte (relation many-to-many avec les canaux)
    |--------------------------------------------------------------------------
    */

    public function reglesAlerte()
    {
        $reglesAlerte = RegleAlerte::with('canaux')->orderBy('decalage_jours')->get();
        $canaux = CanalNotification::where('actif', true)->orderBy('libelle')->get();

        return view('parametre.regle_alerte', compact('reglesAlerte', 'canaux'));
    }

    public function storeRegleAlerte(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'regle_alerte.modifier' : 'regle_alerte.creer');

        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:20',
                $id ? "unique:regles_alerte,code,{$id}" : 'unique:regles_alerte,code',
            ],
            'libelle' => ['required', 'string', 'max:255'],
            'decalage_jours' => ['required', 'integer'],
            'actif' => ['required', 'boolean'],
            'canaux' => ['required', 'array', 'min:1'],
            'canaux.*' => ['exists:canaux_notification,id'],
            'destinataires' => ['required', 'array', 'min:1'],
            'destinataires.*' => ['string', Rule::in(array_keys(RegleAlerte::DESTINATAIRES))],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $canauxIds = $donnees['canaux'];
        $donnees['destinataires'] = array_values(array_unique($donnees['destinataires'])); // liste JSON propre
        unset($donnees['canaux']);

        if ($id) {
            $regle = RegleAlerte::findOrFail($id);
            $regle->update($donnees);
            $message = 'Règle d\'alerte mise à jour avec succès !';
        } else {
            $regle = RegleAlerte::create($donnees);
            $message = 'Règle d\'alerte créée avec succès !';
        }

        $regle->canaux()->sync($canauxIds);

        return redirect()->back()->with('success', $message);
    }

    public function getRegleAlerte($id)
    {
        $regle = RegleAlerte::with('canaux')->findOrFail($id);

        return response()->json([
            'id' => $regle->id,
            'code' => $regle->code,
            'libelle' => $regle->libelle,
            'decalage_jours' => $regle->decalage_jours,
            'destinataires' => $regle->destinataires ?? [],
            'actif' => $regle->actif,
            'canaux_ids' => $regle->canaux->pluck('id'),
        ]);
    }

    public function deleteRegleAlerte($id)
    {
        Gate::authorize('regle_alerte.supprimer');
        $regle = RegleAlerte::find($id);

        if (! $regle) {
            return response()->json(['status' => false, 'message' => 'Règle d\'alerte introuvable.'], 404);
        }

        $regle->delete();

        return response()->json(['status' => true, 'message' => 'Règle d\'alerte supprimée avec succès.']);
    }
}
