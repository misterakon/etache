<?php

namespace App\Support;

use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\User;
use Illuminate\Support\Collection;

/** Création des comptes utilisateurs des agents, un par un ou en masse, avec un mot de passe provisoire à changer. */
class CreationComptes
{
    public static function motDePasse(int $longueur = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $mdp = '';
        for ($i = 0; $i < $longueur; $i++) {
            $mdp .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $mdp;
    }

    /** @return array{nom: string, matricule: ?string, email: string, mot_de_passe: string, profil: string}|string un tableau si créé, sinon la raison du refus */
    public static function pour(Personnel $agent, ProfilAcces $profil): array|string
    {
        if (blank($agent->email)) {
            return "pas d'adresse e-mail sur la fiche";
        }
        if (User::where('personnel_id', $agent->id)->exists()) {
            return 'cet agent a déjà un compte';
        }
        if (User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($agent->email))])->exists()) {
            return 'cette adresse e-mail est déjà utilisée par un autre compte';
        }

        $mdp = self::motDePasse();
        User::create([
            'name' => $agent->nomComplet(), 'email' => trim($agent->email), 'password' => $mdp, 'personnel_id' => $agent->id,
            'profil_acces_id' => $profil->id, 'actif' => true, 'mot_de_passe_provisoire' => true,
        ]);
        Journal::noter('personnel', 'creation', "Compte utilisateur créé pour {$agent->nomComplet()} (profil {$profil->libelle})", $agent, $agent, ['profil' => $profil->slug]);

        return ['nom' => $agent->nomComplet(), 'matricule' => $agent->matricule, 'email' => trim($agent->email), 'mot_de_passe' => $mdp, 'profil' => $profil->libelle];
    }

    /** @return array{crees: list<array>, ignores: list<array{nom: string, raison: string}>} */
    public static function enMasse(Collection $agents, ProfilAcces $profil): array
    {
        $crees = [];
        $ignores = [];
        foreach ($agents as $agent) {
            $r = self::pour($agent, $profil);
            if (is_array($r)) {
                $crees[] = $r;
            } else {
                $ignores[] = ['nom' => $agent->nomComplet(), 'raison' => $r];
            }
        }

        return ['crees' => $crees, 'ignores' => $ignores];
    }
}
