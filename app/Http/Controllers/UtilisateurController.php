<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    public function comptes()
    {
        $comptes = User::with(['personnel', 'profilAcces'])->orderBy('name')->get();
        $personnels = Personnel::where('actif', true)->with(['user:id,personnel_id', 'service:id,nom'])->orderBy('nom')->get();
        $profilsAcces = ProfilAcces::where('actif', true)->orderBy('libelle')->get();

        return view('utilisateur.compte', compact('comptes', 'personnels', 'profilsAcces'));
    }

    public function storeCompte(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'comptes_utilisateurs.modifier' : 'comptes_utilisateurs.creer');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                $id ? "unique:users,email,{$id}" : 'unique:users,email',
            ],
            // Mot de passe obligatoire a la creation, optionnel a la modification
            // (laisse vide = mot de passe inchange)
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8'],
            'personnel_id' => ['nullable', 'exists:personnel,id'],
            'profil_acces_id' => ['required', 'exists:profils_acces,id'],
            'actif' => ['required', 'boolean'],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        // Seul un administrateur peut créer ou modifier un compte administrateur (ou attribuer ce profil)
        $profilDemande = ProfilAcces::find($donnees['profil_acces_id']);
        $cible = $id ? User::with('profilAcces')->find($id) : null;
        abort_if(
            ! $request->user()->hasRole('ADMIN') && ($profilDemande?->slug === ProfilAcces::ADMINISTRATEUR || $cible?->profilAcces?->slug === ProfilAcces::ADMINISTRATEUR),
            403, 'Seul un administrateur peut créer ou modifier un compte administrateur.'
        );

        if (empty($donnees['password'])) {
            unset($donnees['password']);
        } else {
            $donnees['password'] = Hash::make($donnees['password']);
        }

        if ($id) {
            $compte = User::findOrFail($id);
            $compte->update($donnees);
            $message = 'Compte utilisateur mis à jour avec succès !';
        } else {
            $compte = User::create($donnees);
            $message = 'Compte utilisateur créé avec succès !';
        }

        // Garde-fou (sans bloquer) : un RD sans mandat de direction agit comme un RA
        if ($compte->profilSlug() === ProfilAcces::RESPONSABLE_DIRECTION && $compte->sansMandatDirection()) {
            $message .= " Attention : aucune direction n'est rattachée à ce compte, il agira comme un responsable de tâche tant qu'un mandat n'est pas créé.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function getCompte($id)
    {
        // password est deja dans $hidden sur le modele User : jamais renvoye en JSON
        $compte = User::findOrFail($id);

        return response()->json($compte);
    }

    public function deleteCompte($id)
    {
        Gate::authorize('comptes_utilisateurs.supprimer');
        $compte = User::with('profilAcces')->find($id);

        if (! $compte) {
            return response()->json(['status' => false, 'message' => 'Compte introuvable.'], 404);
        }

        abort_if(! auth()->user()->hasRole('ADMIN') && $compte->profilAcces?->slug === ProfilAcces::ADMINISTRATEUR, 403, 'Seul un administrateur peut supprimer un compte administrateur.');

        if ($compte->id === auth()->id()) {
            return response()->json(['status' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        $compte->delete();

        return response()->json(['status' => true, 'message' => 'Compte utilisateur supprimé avec succès.']);
    }
}
