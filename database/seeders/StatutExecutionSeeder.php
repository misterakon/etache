<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatutExecutionSeeder extends Seeder
{
    /**
     * Statuts d'exécution d'une tâche.
     * À ne pas confondre avec la situation temporelle (calculée automatiquement).
     */
    public function run(): void
    {
        $statuts = [
            [
                'code' => '001',
                'libelle' => 'Non démarrée',
                'couleur' => '#5B6B84',
            ],
            [
                'code' => '002',
                'libelle' => 'En cours',
                'couleur' => '#2B6CB0',
            ],
            [
                'code' => '003',
                'libelle' => 'Bloquée',
                'couleur' => '#A73B34',
            ],
            [
                'code' => '004',
                'libelle' => 'Suspendue',
                'couleur' => '#B8790F',
            ],
            [
                'code' => '005',
                'libelle' => 'Terminée',
                'couleur' => '#2E7D5B',
            ],
            [
                'code' => '006',
                'libelle' => 'Annulée',
                'couleur' => '#8A8A8A',
            ],
        ];

        foreach ($statuts as $statut) {
            DB::table('statuts_execution')->updateOrInsert(
                ['code' => $statut['code']],
                array_merge($statut, [
                    'actif' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
