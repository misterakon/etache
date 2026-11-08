<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SituationTemporelleSeeder extends Seeder
{
    /**
     * Situation temporelle : calculée automatiquement par le moteur d'alertes,
     * distincte du statut d'exécution saisi par le responsable.
     */
    public function run(): void
    {
        $situations = [
            ['code' => '001', 'libelle' => 'Normal', 'couleur' => '#5B6B84'],
            ['code' => '002', 'libelle' => 'Échéance proche', 'couleur' => '#B8790F'],
            ['code' => '003', 'libelle' => 'À risque', 'couleur' => '#C2410C'],
            ['code' => '004', 'libelle' => 'En retard', 'couleur' => '#A73B34'],
            ['code' => '005', 'libelle' => 'Terminée', 'couleur' => '#2E7D5B'],
        ];

        foreach ($situations as $situation) {
            DB::table('situations_temporelles')->updateOrInsert(
                ['code' => $situation['code']],
                array_merge($situation, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
