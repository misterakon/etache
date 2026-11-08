<?php

namespace App\Support;

use App\Models\Personnel;

/** Complétude d'une fiche agent : champs essentiels et champs à compléter. */
class CompletudeFiche
{
    /** clé => [libellé, essentiel] */
    public const CHAMPS = [
        'matricule' => ['Matricule', true],
        'nom' => ['Nom', true],
        'prenom' => ['Prénom', true],
        'date_naissance' => ['Date de naissance', true],
        'genre_id' => ['Genre', true],
        'email' => ['E-mail', true],
        'telephone' => ['Téléphone', true],
        'type_agent_id' => ["Type d'agent", true],
        'emploi_id' => ['Emploi', true],
        'date_prise_service' => ['Date de prise de service', true],
        'rattachement' => ['Rattachement (service, poste ou Direction Générale)', true],
        'lieu_naissance' => ['Lieu de naissance', false],
        'civilite_id' => ['Civilité', false],
        'statut_matrimonial_id' => ['Statut matrimonial', false],
        'lieu_habitation' => ["Lieu d'habitation", false],
        'grade_id' => ['Grade', false],
        'classe_id' => ['Classe', false],
        'echelon_id' => ['Échelon', false],
    ];

    /**
     * @return array{score: int, manquants: list<string>, essentiels: list<string>}
     *                                                                              (charger au préalable : mandats actifs, pour le rattachement)
     */
    public static function analyser(Personnel $agent): array
    {
        $manquants = [];
        $essentiels = [];
        foreach (self::CHAMPS as $cle => [$libelle, $essentiel]) {
            $present = $cle === 'rattachement'
                ? ($agent->service_id || $agent->fonction_id || $agent->relationLoaded('mandats') && $agent->mandats->isNotEmpty())
                : filled($agent->{$cle});
            if (! $present) {
                $manquants[] = $libelle;
                if ($essentiel) {
                    $essentiels[] = $libelle;
                }
            }
        }

        return ['score' => (int) round((count(self::CHAMPS) - count($manquants)) / count(self::CHAMPS) * 100), 'manquants' => $manquants, 'essentiels' => $essentiels];
    }
}
