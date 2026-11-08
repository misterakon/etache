<?php

namespace App\Http\Controllers;

use App\Models\Fonction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/** Fonctions (directeur général, DGA, directeur, sous-directeur, chef de service, agent, chargé d'études...). */
class FonctionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('fonction.voir');

        return view('dgepn.fonction', ['fonctions' => Fonction::withCount('personnel')->orderBy('ordre')->get()]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'fonction.modifier' : 'fonction.creer');
        $fonction = $id ? Fonction::findOrFail($id) : null;

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:30', 'alpha_dash', $id ? "unique:fonctions,code,{$id}" : 'unique:fonctions,code'],
            'libelle' => ['required', 'string', 'max:100', $id ? "unique:fonctions,libelle,{$id}" : 'unique:fonctions,libelle'],
            'categorie' => ['required', 'in:'.implode(',', array_keys(Fonction::CATEGORIES))],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ], ['code.unique' => 'Ce code existe déjà.', 'libelle.unique' => 'Cette fonction existe déjà.', 'code.alpha_dash' => 'Code : lettres, chiffres, tirets et soulignés seulement.']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $d = $validator->validated();
        $d['code'] = mb_strtoupper($d['code']);

        if ($fonction?->systeme) {
            // Fonction d'origine : la catégorie et le niveau de mandat ne changent pas, le code non plus (la logique s'y appuie)
            $d['categorie'] = $fonction->categorie;
            $d['code'] = $fonction->code;
        } elseif ($d['categorie'] === Fonction::CAT_MANDAT) {
            return redirect()->back()->withInput()->withErrors(['categorie' => "Les postes à titulaire unique sont ceux de l'organigramme (directeur général, DGA, directeur, sous-directeur, chef de service) : choisissez « Rattaché à un service » ou « Rattaché à la Direction Générale »."]);
        }
        $d['titulaire_unique'] = $d['categorie'] === Fonction::CAT_MANDAT;

        $fonction ? $fonction->update($d) : Fonction::create($d);

        return redirect()->back()->with('success', $fonction ? 'Fonction mise à jour.' : 'Fonction créée.');
    }

    public function delete(Request $request, $id)
    {
        Gate::authorize('fonction.supprimer');
        $fonction = Fonction::withCount('personnel')->findOrFail($id);

        if ($fonction->systeme) {
            return response()->json(['status' => false, 'message' => "Fonction d'origine : elle ne peut pas être supprimée (désactivez-la si besoin)."], 422);
        }
        if ($fonction->personnel_count > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$fonction->personnel_count} agent(s) ont cette fonction. Désactivez-la plutôt."], 422);
        }
        $fonction->delete();

        return response()->json(['status' => true, 'message' => 'Fonction supprimée.']);
    }
}
