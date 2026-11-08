<?php

namespace App\Support;

use App\Models\Personnel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/** Détection des doublons de fiches : même e-mail, même téléphone, même nom et prénom (date de naissance compatible). */
class DoublonsPersonnel
{
    public static function norme(?string $texte): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower(Str::ascii((string) $texte))));
    }

    public static function chiffres(?string $telephone): string
    {
        $c = preg_replace('/\D+/', '', (string) $telephone);

        return strlen($c) >= 8 ? substr($c, -8) : '';
    }

    /** Premier agent existant qui ressemble aux données saisies, avec la raison. */
    public static function index(): Collection
    {
        return Personnel::query()->get(['id', 'nom', 'prenom', 'matricule', 'date_naissance', 'email', 'telephone', 'telephone_2']);
    }

    public static function trouver(array $donnees, ?int $ignorerId = null, ?Collection $index = null): ?array
    {
        $agents = ($index ?? self::index())->when($ignorerId, fn ($c) => $c->where('id', '!=', $ignorerId));

        $email = mb_strtolower(trim((string) ($donnees['email'] ?? '')));
        $tel = self::chiffres($donnees['telephone'] ?? null);
        $nom = self::norme($donnees['nom'] ?? null);
        $prenom = self::norme($donnees['prenom'] ?? null);
        $naissance = ! empty($donnees['date_naissance']) ? substr((string) $donnees['date_naissance'], 0, 10) : null;

        foreach ($agents as $a) {
            if ($email !== '' && mb_strtolower((string) $a->email) === $email) {
                return ['agent' => $a, 'raison' => 'même adresse e-mail'];
            }
            if ($tel !== '' && ($tel === self::chiffres($a->telephone) || $tel === self::chiffres($a->telephone_2))) {
                return ['agent' => $a, 'raison' => 'même numéro de téléphone'];
            }
            if ($nom !== '' && $nom === self::norme($a->nom) && $prenom === self::norme($a->prenom)
                && ($naissance === null || $a->date_naissance === null || $a->date_naissance->toDateString() === $naissance)) {
                return ['agent' => $a, 'raison' => 'même nom et même prénom'.($naissance && $a->date_naissance ? ' et même date de naissance' : '')];
            }
        }

        return null;
    }

    /**
     * Groupes de doublons probables parmi les fiches existantes.
     *
     * @return list<array{raison: string, agents: Collection}>
     */
    public static function groupes(): array
    {
        $agents = Personnel::nonArchives()->with('service:id,nom')->get();
        $groupes = [];
        $deja = [];

        $ajouter = function (string $raison, Collection $membres) use (&$groupes, &$deja) {
            if ($membres->count() < 2) {
                return;
            }
            $cle = $membres->pluck('id')->sort()->implode('-');
            if (isset($deja[$cle])) {
                return;
            }
            $deja[$cle] = true;
            $groupes[] = ['raison' => $raison, 'agents' => $membres->values()];
        };

        foreach ($agents->filter(fn ($a) => filled($a->email))->groupBy(fn ($a) => mb_strtolower(trim($a->email))) as $email => $m) {
            $ajouter("même adresse e-mail ({$email})", $m);
        }
        foreach ($agents->groupBy(fn ($a) => self::chiffres($a->telephone))->reject(fn ($m, $k) => $k === '') as $m) {
            $ajouter('même numéro de téléphone', $m);
        }
        foreach ($agents->groupBy(fn ($a) => self::norme($a->nom).'|'.self::norme($a->prenom))->reject(fn ($m, $k) => $k === '|') as $m) {
            $ajouter('même nom et même prénom', $m);
        }

        return $groupes;
    }
}
