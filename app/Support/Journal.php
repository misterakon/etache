<?php

namespace App\Support;

use App\Models\JournalActivite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * Écriture dans le journal d'activité unifié.
 *
 *   Journal::noter('fichiers', 'depot', 'Fichier déposé : rapport.pdf', $piece, $tache, ['version' => 1]);
 *
 * Une panne du journal ne bloque pas l'action de l'utilisateur : elle est enregistrée dans les logs de Laravel.
 * Ne jamais y mettre de donnée médicale.
 */
class Journal
{
    public static function noter(string $module, string $evenement, string $resume, ?Model $sujet = null, ?Model $parent = null, array $details = []): void
    {
        try {
            $user = auth()->user();

            JournalActivite::create([
                'user_id' => $user?->id,
                'acteur_nom' => $user?->name,
                'module' => $module,
                'evenement' => $evenement,
                'sujet_type' => $sujet?->getMorphClass(),
                'sujet_id' => $sujet?->getKey(),
                'parent_type' => $parent?->getMorphClass(),
                'parent_id' => $parent?->getKey(),
                'resume' => Str::limit($resume, 480, '…'),
                'details' => $details ?: null,
                'ip' => request()?->ip(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
