<?php

namespace App\Http\Controllers;

use App\Models\DelaiCourrier;
use App\Models\InstructionCourrier;
use App\Models\Personnel;
use App\Models\PosteImputation;
use App\Models\TypeCourrier;
use App\Models\UrgenceCourrier;
use App\Support\DestinatairesImputation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/** Paramétrage du courrier (ADMIN) : types de courrier, urgences et délais. */
class ParametrageCourrierController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('parametrage_courrier.voir');

        return view('courrier.parametrage', [
            'types' => TypeCourrier::withCount('courriers')->orderBy('ordre')->get(),
            'urgences' => UrgenceCourrier::withCount('courriers')->orderBy('ordre')->get(),
            'instructions' => InstructionCourrier::orderBy('ordre')->get(),
            'delais' => DelaiCourrier::orderBy('ordre')->get(),
            'postes' => PosteImputation::whereNotNull('niveau')->orderBy('ordre')->get(),
            'positionsOrganigramme' => DestinatairesImputation::postesOrganigramme(),
            'agents' => Personnel::where('actif', true)->orderBy('nom')->orderBy('prenom')->get(),
        ]);
    }

    public function storeType(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'parametrage_courrier.modifier' : 'parametrage_courrier.creer');

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:20', $id ? "unique:types_courrier,code,{$id}" : 'unique:types_courrier,code'],
            'libelle' => ['required', 'string', 'max:100', $id ? "unique:types_courrier,libelle,{$id}" : 'unique:types_courrier,libelle'],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ], ['code.unique' => 'Ce code existe déjà.', 'libelle.unique' => 'Ce type existe déjà.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $donnees = $validator->validated();
        $donnees['code'] = mb_strtoupper(trim($donnees['code']));

        $id ? TypeCourrier::findOrFail($id)->update($donnees) : TypeCourrier::create($donnees);

        return redirect()->back()->with('success', $id ? 'Type de courrier mis à jour.' : 'Type de courrier créé.');
    }

    public function deleteType(Request $request, $id)
    {
        Gate::authorize('parametrage_courrier.supprimer');
        $type = TypeCourrier::withCount('courriers')->findOrFail($id);

        if ($type->courriers_count > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$type->courriers_count} courrier(s) utilisent ce type. Désactivez-le plutôt."], 422);
        }
        $type->delete();

        return response()->json(['status' => true, 'message' => 'Type supprimé.']);
    }

    public function storeUrgence(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'parametrage_courrier.modifier' : 'parametrage_courrier.creer');

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:20', $id ? "unique:urgences_courrier,code,{$id}" : 'unique:urgences_courrier,code'],
            'libelle' => ['required', 'string', 'max:100', $id ? "unique:urgences_courrier,libelle,{$id}" : 'unique:urgences_courrier,libelle'],
            'delai_jours' => ['required', 'integer', 'min:1', 'max:365'],
            'couleur' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ], ['code.unique' => 'Ce code existe déjà.', 'libelle.unique' => 'Cette urgence existe déjà.', 'couleur.regex' => 'Couleur invalide.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $donnees = $validator->validated();
        $donnees['code'] = mb_strtoupper(trim($donnees['code']));

        $id ? UrgenceCourrier::findOrFail($id)->update($donnees) : UrgenceCourrier::create($donnees);

        return redirect()->back()->with('success', $id ? 'Urgence mise à jour.' : 'Urgence créée.');
    }

    public function deleteUrgence(Request $request, $id)
    {
        Gate::authorize('parametrage_courrier.supprimer');
        $urgence = UrgenceCourrier::withCount('courriers')->findOrFail($id);

        if ($urgence->courriers_count > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$urgence->courriers_count} courrier(s) ont cette urgence. Désactivez-la plutôt."], 422);
        }
        $urgence->delete();

        return response()->json(['status' => true, 'message' => 'Urgence supprimée.']);
    }

    /* ---------- Instructions du Directeur Général ---------- */

    public function storeInstruction(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'parametrage_courrier.modifier' : 'parametrage_courrier.creer');

        $validator = Validator::make($request->all(), [
            'libelle' => ['required', 'string', 'max:100', $id ? "unique:instructions_courrier,libelle,{$id}" : 'unique:instructions_courrier,libelle'],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ], ['libelle.unique' => 'Cette instruction existe déjà.']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id ? InstructionCourrier::findOrFail($id)->update($validator->validated()) : InstructionCourrier::create($validator->validated());

        return redirect()->back()->with('success', $id ? 'Instruction mise à jour.' : 'Instruction créée.');
    }

    public function deleteInstruction(Request $request, $id)
    {
        Gate::authorize('parametrage_courrier.supprimer');
        $instruction = InstructionCourrier::findOrFail($id);
        $nb = DB::table('imputation_instruction')->where('instruction_id', $instruction->id)->count();
        if ($nb > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$nb} imputation(s) utilisent cette instruction. Désactivez-la plutôt."], 422);
        }
        $instruction->delete();

        return response()->json(['status' => true, 'message' => 'Instruction supprimée.']);
    }

    /* ---------- Délais de traitement ---------- */

    public function storeDelai(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'parametrage_courrier.modifier' : 'parametrage_courrier.creer');

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:20', $id ? "unique:delais_courrier,code,{$id}" : 'unique:delais_courrier,code'],
            'libelle' => ['required', 'string', 'max:60', $id ? "unique:delais_courrier,libelle,{$id}" : 'unique:delais_courrier,libelle'],
            'saisie_libre' => ['required', 'boolean'],
            'delai_jours' => ['required_if:saisie_libre,0', 'nullable', 'integer', 'min:0', 'max:365'],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ], ['code.unique' => 'Ce code existe déjà.', 'libelle.unique' => 'Ce délai existe déjà.', 'delai_jours.required_if' => 'Indiquez le nombre de jours.']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $donnees = $validator->validated();
        $donnees['code'] = mb_strtoupper(trim($donnees['code']));
        if ($donnees['saisie_libre']) {
            $donnees['delai_jours'] = null;
        }

        $id ? DelaiCourrier::findOrFail($id)->update($donnees) : DelaiCourrier::create($donnees);

        return redirect()->back()->with('success', $id ? 'Délai mis à jour.' : 'Délai créé.');
    }

    public function deleteDelai(Request $request, $id)
    {
        Gate::authorize('parametrage_courrier.supprimer');
        $delai = DelaiCourrier::findOrFail($id);
        $nb = DB::table('imputations')->where('delai_id', $delai->id)->count();
        if ($nb > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$nb} imputation(s) utilisent ce délai. Désactivez-le plutôt."], 422);
        }
        $delai->delete();

        return response()->json(['status' => true, 'message' => 'Délai supprimé.']);
    }

    /* ---------- Postes destinataires (DGA, chargé d'études, service courrier...) ---------- */

    public function storePoste(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'parametrage_courrier.modifier' : 'parametrage_courrier.creer');
        $positions = collect(DestinatairesImputation::postesOrganigramme())->keyBy('cle');

        $validator = Validator::make($request->all(), [
            'position' => ['required', 'string', 'in:'.$positions->keys()->implode(',')],
            'peut_reimputer' => ['required', 'boolean'],
            'ordre' => ['required', 'integer', 'min:1', 'max:999'],
            'actif' => ['required', 'boolean'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $donnees = $validator->validated();
        $position = $positions->get($donnees['position']);
        [$niveau, $uniteId] = explode(':', $donnees['position'], 2);
        $uniteType = $position['type'];
        $uniteId = $uniteType ? (int) $uniteId : null;
        $configuration = PosteImputation::where('niveau', $niveau)
            ->when($uniteType === null, fn ($query) => $query->whereNull('unite_type'), fn ($query) => $query->where('unite_type', $uniteType))
            ->when($uniteId === null, fn ($query) => $query->whereNull('unite_id'), fn ($query) => $query->where('unite_id', $uniteId))
            ->when($id, fn ($query) => $query->where('id', '!=', $id))
            ->first();
        if ($configuration) {
            return redirect()->back()->withErrors(['position' => 'Ce poste de l’organigramme est déjà configuré.'])->withInput();
        }

        unset($donnees['position']);
        $donnees = array_merge($donnees, [
            'niveau' => $niveau,
            'unite_type' => $uniteType,
            'unite_id' => $uniteId,
            'personnel_id' => null,
            'libelle' => $position['libelle'],
        ]);

        $id ? PosteImputation::findOrFail($id)->update($donnees) : PosteImputation::create($donnees);

        return redirect()->back()->with('success', $id ? 'Poste mis à jour.' : 'Poste créé.');
    }

    public function deletePoste(Request $request, $id)
    {
        Gate::authorize('parametrage_courrier.supprimer');
        $poste = PosteImputation::findOrFail($id);
        $nb = DB::table('diligences')->where('destinataire_type', 'poste')->where('destinataire_id', $poste->id)->count();
        if ($nb > 0) {
            return response()->json(['status' => false, 'message' => "Impossible de supprimer : {$nb} diligence(s) sont rattachées à ce poste. Désactivez-le plutôt."], 422);
        }
        $poste->delete();

        return response()->json(['status' => true, 'message' => 'Poste supprimé.']);
    }
}
