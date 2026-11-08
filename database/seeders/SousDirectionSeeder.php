<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SousDirectionSeeder extends Seeder
{
    /**
     * Necessite que DirectionSeeder ait deja ete joue.
     */
    public function run(): void
    {
        $sousDirectionsParDirection = [
            'DSPQ' => [
                'Sous-direction du Suivi des EPN des Secteurs de la Santé, de l\'Education et de la Formation',
                'Sous-direction du Suivi des EPN des Secteurs Economiques, Techniques et Environnementaux',
                'Sous-direction du Suivi des EPN des Secteurs Administratifs, Sociaux et assimilés',
            ],
            'DSAR' => [
                'Sous-direction de la Stratégie',
                'Sous-direction de l\'Audit, du Contrôle et des Risques',
            ],
            'DAJ' => [
                'Sous-direction des Affaires Juridiques des EPN des Secteurs de la Santé, de l\'Education et de la Formation',
                'Sous-direction des Affaires Juridiques des EPN des Secteurs Economiques, Techniques et Environnementaux',
                'Sous-direction des Affaires Juridiques des EPN des Secteurs Administratifs, Sociaux et Assimilés',
            ],
            'DI' => [
                'Sous-Direction des Développements et des Etudes Informatiques',
                'Sous-Direction de l\'Exploitation et du Réseau',
                'Sous-Direction du Support et de la Production',
            ],
            'DPC' => [
                'Sous-direction du Personnel et de la Formation',
                'Sous-direction de la Communication',
            ],
        ];

        foreach ($sousDirectionsParDirection as $codeDirection => $listeSousDirections) {
            $directionId = DB::table('directions')->where('code', $codeDirection)->value('id');

            if (! $directionId) {
                continue; // DirectionSeeder pas encore joue, on ignore plutot que de planter
            }

            foreach ($listeSousDirections as $nom) {
                DB::table('sous_directions')->updateOrInsert(
                    ['direction_id' => $directionId, 'nom' => $nom],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
