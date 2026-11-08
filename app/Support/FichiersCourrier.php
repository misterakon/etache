<?php

namespace App\Support;

use App\Models\PieceJointe;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/** Dépôt et lecture des fichiers du courrier (projets de réponse, signatures, scans de départ). Versions conservées. */
class FichiersCourrier
{
    public static function disque()
    {
        return Storage::disk(config('pieces_jointes.disque'));
    }

    /** @param list<string> $extensionsAutorisees */
    public static function deposer(Model $cible, UploadedFile $fichier, string $categorie, array $extensionsAutorisees, User $user, string $dossier = 'courriers'): PieceJointe
    {
        $ext = strtolower($fichier->getClientOriginalExtension());
        $mimes = config('pieces_jointes.extensions')[$ext] ?? [];
        if (! in_array($ext, $extensionsAutorisees, true) || ! in_array($fichier->getMimeType(), $mimes, true)) {
            throw new DomainException('Type de fichier non accepté (formats : '.implode(', ', array_map('strtoupper', $extensionsAutorisees)).').');
        }
        if ($fichier->getSize() > Reglages::get('courrier.scan_taille_max_ko') * 1024) {
            throw new DomainException('Le fichier dépasse la taille maximale autorisée ('.Reglages::get('courrier.scan_taille_max_ko').' Ko).');
        }

        $chemin = $fichier->storeAs($dossier.'/'.now()->format('Y'), Str::random(40).'.'.$ext, config('pieces_jointes.disque'));
        if (! $chemin) {
            throw new RuntimeException("Le fichier n'a pas pu être enregistré.");
        }

        $ancien = $cible->morphMany(PieceJointe::class, 'attachable')->where('categorie', $categorie)->where('actuelle', true)->first();
        $piece = PieceJointe::create([
            'attachable_type' => $cible->getMorphClass(), 'attachable_id' => $cible->id, 'categorie' => $categorie,
            'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''), 'chemin' => $chemin, 'extension' => $ext,
            'mime' => $fichier->getMimeType(), 'taille' => $fichier->getSize(),
            'version' => $ancien ? $ancien->version + 1 : 1, 'racine_id' => $ancien?->racine(), 'actuelle' => true, 'depose_par' => $user->id,
        ]);
        $ancien?->update(['actuelle' => false]);

        return $piece;
    }

    /** Réponse HTTP : affichage dans le navigateur (aperçu) ou téléchargement. */
    public static function servir(PieceJointe $piece, string $nom, bool $apercu)
    {
        abort_unless(self::disque()->exists($piece->chemin), 404, 'Fichier introuvable.');

        if ($apercu && $piece->estApercevable()) {
            return response()->file(self::disque()->path($piece->chemin), [
                'Content-Type' => $piece->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.addslashes($nom.'.'.$piece->extension).'"',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
            ]);
        }

        return self::disque()->download($piece->chemin, $nom.'.'.$piece->extension);
    }
}
