<?php

namespace App\Models\Concerns;

use App\Models\MandatResponsable;
use App\Models\ProfilAcces;
use Illuminate\Support\Collection;

/**
 * Profil d'acces de l'utilisateur, avec la meme syntaxe que spatie/laravel-permission :
 *
 *   auth()->user()->hasRole('ADMIN')
 *   auth()->user()->hasRole(['ADMIN', 'DG'])        ou  hasRole('ADMIN|DG')
 *   auth()->user()->hasAnyRole('ADMIN', 'DG')
 *   auth()->user()->profilSlug()                    // 'ADMIN', 'DG', 'RD', 'RA' ou null
 *   auth()->user()->profilAcces?->libelle           // libelle affichable
 *
 * S'appuie sur la relation existante User::profilAcces() (table profils_acces, colonne slug).
 * A utiliser dans le modele User :  use App\Models\Concerns\ADesProfils;  puis  use ADesProfils;
 */
trait ADesProfils
{
    private ?Collection $directionsDirigeesCache = null;

    /** Profil réellement exercé : un RD sans mandat de direction en cours agit comme un RA. */
    public function profilEffectif(): ?string
    {
        return $this->sansMandatDirection() ? ProfilAcces::RESPONSABLE_TACHE : $this->profilSlug();
    }

    /** Compte RD sans mandat de responsable de direction en cours. */
    public function sansMandatDirection(): bool
    {
        return mb_strtoupper((string) $this->profilSlug()) === ProfilAcces::RESPONSABLE_DIRECTION
            && $this->directionsDirigees()->isEmpty();
    }

    /** Ids des directions dont l'utilisateur est le responsable en poste aujourd'hui (via les mandats). */
    public function directionsDirigees(): Collection
    {
        return $this->directionsDirigeesCache ??= $this->personnel_id
            ? MandatResponsable::where('niveau', MandatResponsable::NIVEAU_DIRECTION)
                ->where('personnel_id', $this->personnel_id)->actifA()
                ->pluck('direction_id')->unique()
                ->filter(fn ($id) => MandatResponsable::enPoste(MandatResponsable::NIVEAU_DIRECTION, (int) $id)?->personnel_id == $this->personnel_id)
                ->map(fn ($id) => (int) $id)->values()
            : collect();
    }

    /** Slug du profil de l'utilisateur (ProfilAcces::ADMINISTRATEUR, DG, ...) ou null s'il n'en a pas. */
    public function profilSlug(): ?string
    {
        return $this->profilAcces?->slug;
    }

    /** L'utilisateur a-t-il l'un de ces profils ? (comparaison insensible a la casse) */
    public function hasRole(string|array $roles): bool
    {
        return $this->hasAnyRole($roles);
    }

    public function hasAnyRole(string|array ...$roles): bool
    {
        $slug = $this->profilEffectif();

        return $slug !== null
            && $slug !== ''
            && in_array(mb_strtoupper($slug), $this->normaliserRoles($roles), true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(ProfilAcces::ADMINISTRATEUR);
    }

    /** Aplatit ['ADMIN|DG', ['RD', 'RA']] en ['ADMIN', 'DG', 'RD', 'RA'] (majuscules). */
    private function normaliserRoles(array $roles): array
    {
        $liste = [];

        foreach ($roles as $role) {
            foreach ((array) $role as $element) {
                if (is_array($element)) {
                    $liste = array_merge($liste, $this->normaliserRoles($element));
                    continue;
                }

                foreach (preg_split('/\s*[|,]\s*/', trim((string) $element), -1, PREG_SPLIT_NO_EMPTY) as $morceau) {
                    $liste[] = mb_strtoupper($morceau);
                }
            }
        }

        return $liste;
    }
}
