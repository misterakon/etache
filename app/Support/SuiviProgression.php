<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Suivi de progression d'une opération longue (analyse ou import de la matrice).
 *
 * L'opération écrit l'étape en cours (clé, éléments traités, total) dans le cache « file » ; la page interroge
 * régulièrement la route de progression pour animer la barre. Le suivi ne doit jamais faire échouer l'opération :
 * toute erreur d'écriture est ignorée. Chaque suivi est propre à un utilisateur et à un identifiant tiré par la page.
 */
class SuiviProgression
{
    private const FORMAT_ID = '/^[A-Za-z0-9]{20,40}$/';

    private const DUREE_SECONDES = 900;

    private const INTERVALLE = 0.15; // secondes minimum entre deux écritures d'une même étape

    private float $dernier = 0.0;

    private ?string $derniereEtape = null;

    private function __construct(private readonly string $cle) {}

    private static function cle(string $id): string
    {
        return 'import_matrice_progression_'.auth()->id().'_'.$id;
    }

    /** Suivi pour l'identifiant donné, ou null s'il est absent ou invalide. */
    public static function pour(?string $id): ?self
    {
        return $id !== null && preg_match(self::FORMAT_ID, $id) ? new self(self::cle($id)) : null;
    }

    /** Enregistre l'étape en cours (écritures espacées dans une même étape, jamais en fin d'étape). */
    public function maj(string $etape, int $fait = 0, int $total = 0): void
    {
        $maintenant = microtime(true);
        $change = $etape !== $this->derniereEtape;

        if (! $change && $fait < $total && ($maintenant - $this->dernier) < self::INTERVALLE) {
            return;
        }

        $this->derniereEtape = $etape;
        $this->dernier = $maintenant;

        try {
            Cache::store('file')->put($this->cle, ['etape' => $etape, 'fait' => $fait, 'total' => $total], self::DUREE_SECONDES);
        } catch (\Throwable $e) {
            // le suivi est facultatif
        }
    }

    /** Fonction à transmettre aux traitements : ($etape, $fait = 0, $total = 0). */
    public function callback(): \Closure
    {
        return fn (string $etape, int $fait = 0, int $total = 0) => $this->maj($etape, $fait, $total);
    }

    public function terminer(): void
    {
        try {
            Cache::store('file')->forget($this->cle);
        } catch (\Throwable $e) {
        }
    }

    /** Dernier état connu pour l'identifiant donné (null si aucun). */
    public static function lire(string $id): ?array
    {
        if (! preg_match(self::FORMAT_ID, $id)) {
            return null;
        }

        try {
            return Cache::store('file')->get(self::cle($id));
        } catch (\Throwable $e) {
            return null;
        }
    }
}
