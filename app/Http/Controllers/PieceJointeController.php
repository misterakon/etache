<?php

namespace App\Http\Controllers;

use App\Models\PieceJointe;
use App\Models\SousTache;
use App\Models\Tache;
use App\Support\Journal;
use App\Support\PieceJointeDroits;
use App\Support\Reglages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ZipArchive;

class PieceJointeController extends Controller
{
    private function disque()
    {
        return Storage::disk(config('pieces_jointes.disque'));
    }

    private function retour(Tache $tache): string
    {
        return route('feuille_de_route.tache.details', $tache->id).'#livrables';
    }

    /** Dépôt d'un nouveau fichier, ou remplacement d'un fichier existant (remplace_id) : l'ancienne version est conservée. */
    public function store(Request $request)
    {
        // Types autorisés = types techniquement gérés (config) ∩ types retenus dans les Réglages
        $extensions = array_intersect_key(config('pieces_jointes.extensions'), array_flip(Reglages::get('fichiers.extensions')));
        $maxParElement = Reglages::get('fichiers.max_par_element');

        $validator = Validator::make($request->all(), [
            'attachable_type' => ['required', 'in:tache,sous_tache'],
            'attachable_id' => ['required', 'integer'],
            'remplace_id' => ['nullable', 'integer', 'exists:pieces_jointes,id'],
            'fichier' => ['required', 'file', 'max:'.Reglages::get('fichiers.taille_max_ko'), 'extensions:'.implode(',', array_keys($extensions))],
        ], [
            'fichier.required' => 'Choisissez un fichier.',
            'fichier.uploaded' => "Le fichier n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
            'fichier.max' => 'Le fichier dépasse la taille maximale autorisée (:max Ko).',
            'fichier.extensions' => 'Type de fichier non autorisé (PDF, Word, Excel, PowerPoint, images, zip).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $cible = $donnees['attachable_type'] === 'tache'
            ? Tache::findOrFail($donnees['attachable_id'])
            : SousTache::with('tache')->findOrFail($donnees['attachable_id']);
        $tache = $cible instanceof SousTache ? $cible->tache : $cible;

        $droit = PieceJointeDroits::deposer($tache, auth()->user());
        if (! $droit['ok']) {
            return redirect()->to($this->retour($tache))->withErrors(['fichier' => $droit['raison']]);
        }

        $fichier = $request->file('fichier');
        $ext = strtolower($fichier->getClientOriginalExtension());

        // Le contenu réel du fichier doit correspondre à son extension
        if (! in_array($fichier->getMimeType(), $extensions[$ext] ?? [], true)) {
            return redirect()->to($this->retour($tache))->withErrors(['fichier' => "Le contenu du fichier ne correspond pas à son extension (.{$ext})."]);
        }

        $ancienne = null;
        if (! empty($donnees['remplace_id'])) {
            $ancienne = PieceJointe::where('attachable_type', $cible->getMorphClass())
                ->where('attachable_id', $cible->id)
                ->where('actuelle', true)
                ->find($donnees['remplace_id']);
            if (! $ancienne) {
                return redirect()->to($this->retour($tache))->withErrors(['fichier' => 'Le fichier à remplacer est introuvable.']);
            }
        } elseif ($cible->piecesJointes()->count() >= $maxParElement) {
            return redirect()->to($this->retour($tache))->withErrors(['fichier' => 'Maximum de '.$maxParElement.' fichiers atteint.']);
        }

        $chemin = $fichier->storeAs('pieces_jointes/'.now()->format('Y/m'), Str::random(40).'.'.$ext, config('pieces_jointes.disque'));
        if (! $chemin) {
            return redirect()->to($this->retour($tache))->withErrors(['fichier' => "Le fichier n'a pas pu être enregistré."]);
        }

        $piece = DB::transaction(function () use ($cible, $fichier, $ext, $chemin, $ancienne) {
            $piece = PieceJointe::create([
                'attachable_type' => $cible->getMorphClass(),
                'attachable_id' => $cible->id,
                'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''),
                'chemin' => $chemin,
                'extension' => $ext,
                'mime' => $fichier->getMimeType(),
                'taille' => $fichier->getSize(),
                'version' => $ancienne ? $ancienne->version + 1 : 1,
                'racine_id' => $ancienne?->racine(),
                'actuelle' => true,
                'depose_par' => auth()->id(),
            ]);
            $ancienne?->update(['actuelle' => false]);

            return $piece;
        });

        $objet = $cible instanceof SousTache ? ' (sous-tâche « '.$cible->libelle.' »)' : '';
        Journal::noter(
            'fichiers',
            $ancienne ? 'remplacement' : 'depot',
            ($ancienne ? 'Fichier remplacé (v'.$piece->version.') : ' : 'Fichier déposé : ').$piece->nom_original.$objet,
            $piece,
            $tache,
            ['fichier' => $piece->nom_original, 'taille' => $piece->taille, 'version' => $piece->version]
        );

        return redirect()->to($this->retour($tache))->with('success', $ancienne ? 'Fichier remplacé avec succès !' : 'Fichier déposé avec succès !');
    }

    public function telecharger($id)
    {
        return $this->servir(PieceJointe::findOrFail($id), false);
    }

    public function apercu($id)
    {
        $piece = PieceJointe::findOrFail($id);
        abort_unless($piece->estApercevable(), 404);

        return $this->servir($piece, true);
    }

    private function servir(PieceJointe $piece, bool $enLigne)
    {
        $tache = $piece->tache();
        abort_unless($tache && PieceJointeDroits::voir($tache, auth()->user()), 403, "Vous n'avez pas accès à ce fichier.");
        abort_unless($this->disque()->exists($piece->chemin), 404, 'Fichier introuvable sur le serveur.');

        Journal::noter('fichiers', 'telechargement', ($enLigne ? 'Fichier consulté : ' : 'Fichier téléchargé : ').$piece->nom_original, $piece, $tache, ['version' => $piece->version]);

        if ($enLigne) {
            return response()->file($this->disque()->path($piece->chemin), [
                'Content-Type' => $piece->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.addslashes($piece->nom_original).'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return $this->disque()->download($piece->chemin, $piece->nom_original);
    }

    /** Supprime le fichier et toutes ses versions (suppression logique : la trace reste dans le journal). */
    public function destroy($id)
    {
        $piece = PieceJointe::findOrFail($id);
        $tache = $piece->tache();

        if (! $tache || ! PieceJointeDroits::supprimer($piece, $tache, auth()->user())) {
            return response()->json(['status' => false, 'message' => "Vous n'avez pas le droit de supprimer ce fichier."], 403);
        }

        Journal::noter('fichiers', 'suppression', 'Fichier supprimé : '.$piece->nom_original, $piece, $tache, ['fichier' => $piece->nom_original, 'versions' => $piece->chaine()->count()]);
        $piece->chaine()->get()->each->delete();

        return response()->json(['status' => true, 'message' => 'Fichier supprimé.']);
    }

    /** Téléchargement groupé : fichiers de la tâche et de ses sous-tâches. */
    public function zip($id)
    {
        $tache = Tache::findOrFail($id);
        abort_unless(PieceJointeDroits::voir($tache, auth()->user()), 403, "Vous n'avez pas accès à cette tâche.");
        abort_unless(class_exists(ZipArchive::class), 500, "L'extension PHP « zip » n'est pas installée sur le serveur.");

        $sousTaches = $tache->sousTaches()->get()->keyBy('id');
        $pieces = $tache->piecesJointes()->get();
        $piecesSt = PieceJointe::where('attachable_type', (new SousTache)->getMorphClass())
            ->whereIn('attachable_id', $sousTaches->keys())->where('actuelle', true)->get();

        if ($pieces->isEmpty() && $piecesSt->isEmpty()) {
            return redirect()->to($this->retour($tache))->withErrors(['fichier' => 'Aucun fichier à télécharger.']);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'liv');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $noms = [];

        $ajouter = function (PieceJointe $p, string $dossier) use ($zip, &$noms) {
            if (! $this->disque()->exists($p->chemin)) {
                return;
            }
            $nom = ($dossier !== '' ? $dossier.'/' : '').str_replace(['/', '\\'], '-', $p->nom_original);
            for ($i = 2; isset($noms[$nom]); $i++) { // deux fichiers de même nom : on numérote
                $info = pathinfo($p->nom_original);
                $nom = ($dossier !== '' ? $dossier.'/' : '').str_replace(['/', '\\'], '-', $info['filename']).' ('.$i.')'.(isset($info['extension']) ? '.'.$info['extension'] : '');
            }
            $noms[$nom] = true;
            $zip->addFile($this->disque()->path($p->chemin), $nom);
        };

        foreach ($pieces as $p) {
            $ajouter($p, '');
        }
        foreach ($piecesSt as $p) {
            $ajouter($p, 'Sous-taches/'.Str::slug($sousTaches[$p->attachable_id]->libelle ?? 'sous-tache'));
        }
        $zip->close();

        Journal::noter('fichiers', 'telechargement', 'Téléchargement groupé des livrables ('.($pieces->count() + $piecesSt->count()).' fichier(s))', null, $tache);

        return response()->download($tmp, 'livrables_'.$tache->code.'.zip')->deleteFileAfterSend(true);
    }
}
