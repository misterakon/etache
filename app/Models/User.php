<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\ADesProfils;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'personnel_id', 'profil_acces_id', 'actif', 'mot_de_passe_provisoire'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use ADesProfils, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
            'mot_de_passe_provisoire' => 'boolean',
            'mot_de_passe_modifie_le' => 'datetime',
        ];
    }

    /**
     * Le profil d'accès de l'utilisateur (DG, Responsable de direction, etc.).
     */
    public function profilAcces()
    {
        return $this->belongsTo(ProfilAcces::class);
    }

    /**
     * La fiche personnel (annuaire RH) liee a ce compte, si elle existe.
     */
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * Le profil de l'utilisateur lui accorde-t-il ce droit précis (App\Support\Droits) ? Un compte inactif
     * n'a jamais aucun droit. Complète hasRole()/ExigerProfil, sans les remplacer : les deux coexistent
     * pendant que les écrans basculent progressivement vers ce système plus fin.
     */
    public function peut(string $droit): bool
    {
        return $this->actif && $this->profilAcces?->autorise($droit) === true;
    }
}
