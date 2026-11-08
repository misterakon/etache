<?php

namespace App\Models;

use App\Models\Concerns\GenereCodeSequentiel;
use App\Support\Droits;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'slug', 'libelle', 'actif', 'droits'])]
class ProfilAcces extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'profils_acces';

    // Slugs stables, utilisés dans toute la logique métier (contrairement à `code`,
    // qui n'est qu'un compteur technique auto-généré sans signification propre).
    const DG = 'DG';

    const RESPONSABLE_DIRECTION = 'RD';

    const RESPONSABLE_TACHE = 'RA';

    const ADMINISTRATEUR = 'ADMIN';

    const COURRIER = 'COURRIER';

    const AGENT = 'AGENT'; // profil par défaut des comptes créés en masse

    protected function casts(): array
    {
        return ['actif' => 'boolean', 'systeme' => 'boolean', 'droits' => 'array'];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Ex: ProfilAcces::bySlug(ProfilAcces::DG)
     */
    public static function bySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /** Le profil Admin du système (tous les droits, non modifiable, créé par la migration). */
    public static function systeme(): ?self
    {
        return static::where('systeme', true)->first();
    }

    /** Ce profil accorde-t-il ce droit précis ? Un profil « systeme » (Admin) a tous les droits d'office. */
    public function autorise(string $droit): bool
    {
        return $this->systeme === true || in_array($droit, $this->droits ?? [], true);
    }

    /** Nombre de sous-menus auxquels ce profil donne accès (Admin : tous). */
    public function nombrePages(): int
    {
        return $this->systeme ? count(Droits::pages()) : Droits::nombrePagesAccessibles($this->droits ?? []);
    }
}
