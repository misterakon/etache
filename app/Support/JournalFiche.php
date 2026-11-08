<?php

namespace App\Support;

use App\Models\Personnel;

/**
 * Journal des modifications du formulaire agent. Les valeurs de certains champs (date de naissance, téléphones, adresse,
 * filiation) ne sont jamais écrites dans le journal : seul le nom du champ modifié est tracé.
 */
class JournalFiche
{
    public const CHAMPS = [
        'matricule' => 'Matricule', 'nom' => 'Nom', 'prenom' => 'Prénom', 'date_naissance' => 'Date de naissance', 'lieu_naissance' => 'Lieu de naissance',
        'email' => 'E-mail', 'telephone' => 'Téléphone', 'telephone_2' => 'Téléphone 2', 'lieu_habitation' => "Lieu d'habitation",
        'civilite_id' => 'Civilité', 'genre_id' => 'Genre', 'statut_matrimonial_id' => 'Statut matrimonial', 'nombre_enfants' => "Nombre d'enfants",
        'nom_pere' => 'Nom du père', 'nom_mere' => 'Nom de la mère', 'type_agent_id' => "Type d'agent", 'emploi_id' => 'Emploi', 'actif' => 'Actif',
    ];

    private const MASQUES = ['date_naissance', 'telephone', 'telephone_2', 'lieu_habitation', 'nom_pere', 'nom_mere'];

    /** Instantané des champs suivis, à prendre AVANT la modification. */
    public static function avant(Personnel $agent): array
    {
        return collect(array_keys(self::CHAMPS))->mapWithKeys(fn ($c) => [$c => self::valeur($agent->getAttribute($c))])->all();
    }

    public static function modification(Personnel $agent, array $avant): void
    {
        $agent->refresh();
        $champs = [];
        $details = [];
        foreach (self::CHAMPS as $cle => $libelle) {
            $apres = self::valeur($agent->getAttribute($cle));
            if (($avant[$cle] ?? '') === $apres) {
                continue;
            }
            $champs[] = $libelle;
            $details[$libelle] = in_array($cle, self::MASQUES, true) ? 'modifié' : (($avant[$cle] ?? '') !== '' ? $avant[$cle] : '∅').' → '.($apres !== '' ? $apres : '∅');
        }
        if ($champs === []) {
            return;
        }
        Journal::noter('personnel', 'modification', "Fiche de {$agent->nomComplet()} modifiée : ".implode(', ', $champs), $agent, $agent, ['champs' => $details]);
    }

    public static function creation(Personnel $agent): void
    {
        Journal::noter('personnel', 'creation', "Agent {$agent->nomComplet()} créé (matricule {$agent->matricule})", $agent, $agent, ['matricule' => $agent->matricule]);
    }

    private static function valeur($v): string
    {
        if ($v instanceof \DateTimeInterface) {
            return $v->format('Y-m-d');
        }
        if (is_bool($v)) {
            return $v ? '1' : '0';
        }

        return is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}[ T]/', $v) ? substr($v, 0, 10) : trim((string) $v);
    }
}
