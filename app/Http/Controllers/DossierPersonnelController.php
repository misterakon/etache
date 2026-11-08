<?php

namespace App\Http\Controllers;

use App\Models\ListeValeur;
use App\Models\Personnel;
use App\Models\PieceJointe;
use App\Support\Journal;
use App\Support\Reglages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Dossier de l'agent (CV, carte d'identité, diplômes...).
 * Consultation : administrateur, DG et l'agent lui-même (chaque consultation est tracée). Dépôt et suppression : administrateur.
 */
class DossierPersonnelController extends Controller
{
    private function disque()
    {
        return Storage::disk(config('pieces_jointes.disque'));
    }

    private function peutVoir(Request $request, Personnel $agent): bool
    {
        $user = $request->user();

        return $user !== null && ($user->hasRole('ADMIN|DG') || ($user->personnel_id !== null && (int) $user->personnel_id === (int) $agent->id));
    }

    private function piece(Request $request, $id): array
    {
        $piece = PieceJointe::where('attachable_type', (new Personnel)->getMorphClass())->where('categorie', 'like', 'dossier_%')->findOrFail($id);
        $agent = Personnel::findOrFail($piece->attachable_id);
        abort_unless($this->peutVoir($request, $agent), 403, "Vous n'avez pas accès à ce dossier.");

        return [$piece, $agent];
    }

    public function store(Request $request, $personnelId)
    {
        $agent = Personnel::findOrFail($personnelId);
        $user = $request->user();
        $estAdmin = $user?->hasRole('ADMIN') ?? false;
        $estAgentConcerne = $user !== null && $user->personnel_id !== null && (int) $user->personnel_id === (int) $agent->id;
        abort_unless($estAdmin || $estAgentConcerne, 403, "Vous n'êtes pas autorisé à déposer un document dans ce dossier.");

        $extensions = array_intersect_key(config('pieces_jointes.extensions'), array_flip(Reglages::get('personnel.dossier_extensions')));
        $types = ListeValeur::deListe('type_document')->pluck('libelle', 'code');
        $categorie = 'dossier_'.$request->input('type_document');

        $validator = Validator::make($request->all(), [
            'type_document' => ['required', 'in:'.$types->keys()->implode(',')],
            'fichier' => ['required', 'file', 'max:'.Reglages::get('personnel.dossier_taille_max_ko'), 'extensions:'.implode(',', array_keys($extensions))],
        ], [
            'type_document.required' => 'Choisissez le type de document.',
            'fichier.required' => 'Joignez le document.',
            'fichier.uploaded' => "Le document n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
            'fichier.max' => 'Le document dépasse la taille maximale autorisée (:max Ko).',
            'fichier.extensions' => 'Type de fichier non accepté.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dejaDepose = PieceJointe::withTrashed()
            ->where('attachable_type', $agent->getMorphClass())
            ->where('attachable_id', $agent->id)
            ->where('categorie', $categorie)
            ->exists();
        if ($dejaDepose) {
            return redirect()->back()->withErrors(['type_document' => 'Un document de ce type a déjà été déposé. Chaque type de dossier ne peut être envoyé qu’une seule fois.'])->withInput();
        }

        $fichier = $request->file('fichier');
        $ext = strtolower($fichier->getClientOriginalExtension());
        if (! in_array($fichier->getMimeType(), $extensions[$ext] ?? [], true)) {
            return redirect()->back()->withErrors(['fichier' => "Le contenu du fichier ne correspond pas à son extension (.{$ext})."])->withInput();
        }

        $chemin = $fichier->storeAs('dossiers_agents/'.$agent->id, Str::random(40).'.'.$ext, config('pieces_jointes.disque'));

        $piece = PieceJointe::create([
            'attachable_type' => $agent->getMorphClass(), 'attachable_id' => $agent->id, 'categorie' => $categorie,
            'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''), 'chemin' => $chemin, 'extension' => $ext,
            'mime' => $fichier->getMimeType(), 'taille' => $fichier->getSize(), 'version' => 1, 'actuelle' => true, 'depose_par' => $request->user()->id,
        ]);

        Journal::noter('personnel', 'dossier', "Document « {$types[$request->input('type_document')]} » déposé dans le dossier de {$agent->nomComplet()} : {$piece->nom_original}", $piece, $agent);

        return redirect()->back()->with('success', 'Document ajouté au dossier.');
    }

    /** Aperçu dans le navigateur ou téléchargement. */
    public function fichier(Request $request, $id, string $mode = 'apercu')
    {
        [$piece, $agent] = $this->piece($request, $id);
        abort_unless($this->disque()->exists($piece->chemin), 404, 'Fichier introuvable.');

        Journal::noter('personnel', 'consultation', "Dossier de {$agent->nomComplet()} : « {$piece->nom_original} » ".($mode === 'apercu' ? 'consulté' : 'téléchargé'), $piece, $agent);

        if ($mode === 'apercu' && in_array($piece->extension, config('pieces_jointes.apercu'), true)) {
            return response()->file($this->disque()->path($piece->chemin), [
                'Content-Type' => $piece->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.addslashes($piece->nom_original).'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return $this->disque()->download($piece->chemin, $piece->nom_original);
    }

    public function apercu(Request $request, $id)
    {
        return $this->fichier($request, $id, 'apercu');
    }

    public function telecharger(Request $request, $id)
    {
        return $this->fichier($request, $id, 'telechargement');
    }

    public function destroy(Request $request, $id)
    {
        abort_unless($request->user()?->hasRole('ADMIN'), 403, "Seul l'administrateur peut supprimer un document du dossier.");
        [$piece, $agent] = $this->piece($request, $id);

        $piece->delete(); // suppression logique (le fichier reste sur le disque)
        Journal::noter('personnel', 'dossier', "Document « {$piece->nom_original} » retiré du dossier de {$agent->nomComplet()}", $piece, $agent);

        return response()->json(['status' => true, 'message' => 'Document retiré du dossier.']);
    }
}
