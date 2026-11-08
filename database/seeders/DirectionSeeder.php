<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectionSeeder extends Seeder
{
    /**
     * Les 5 directions rattachees a la DGEPN, decret DGEPN, Articles 22 a 26.
     */
    public function run(): void
    {
        $directions = [
            [
                'code' => 'DSPQ',
                'nom' => 'Direction du Suivi de la Performance et de la Qualité',
                'description' => "Assure le suivi et l'évaluation de la performance économique et opérationnelle et de la qualité du service public des établissements publics nationaux et structures assimilées (Article 22).",
            ],
            [
                'code' => 'DSAR',
                'nom' => "Direction de la Stratégie, de l'Audit et des Risques",
                'description' => "Définit et suit la stratégie de gestion du portefeuille, organise les missions d'audit et assure le suivi des risques liés aux établissements publics nationaux (Article 23).",
            ],
            [
                'code' => 'DAJ',
                'nom' => 'Direction des Affaires Juridiques',
                'description' => "Élabore les projets de textes législatifs et réglementaires et assure l'interprétation uniforme des textes relatifs aux établissements publics nationaux (Article 24).",
            ],
            [
                'code' => 'DI',
                'nom' => "Direction de l'Informatique",
                'description' => "Conçoit, développe et administre les applications, systèmes d'information et outils numériques de suivi des établissements publics nationaux (Article 25).",
            ],
            [
                'code' => 'DPC',
                'nom' => 'Direction du Personnel et de la Communication',
                'description' => "Assure la gestion du personnel, la planification des besoins en formation et la communication interne et externe de la DGEPN (Article 26).",
            ],
        ];

        foreach ($directions as $direction) {
            DB::table('directions')->updateOrInsert(
                ['code' => $direction['code']],
                array_merge($direction, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
