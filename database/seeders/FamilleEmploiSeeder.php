<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilleEmploiSeeder extends Seeder
{
    /**
     * Liste de départ — à ajuster selon la nomenclature RH réelle de la DGEPN.
     */
    public function run(): void
    {
        $familles = [
            ['code' => '001', 'libelle' => 'Informatique'],
            ['code' => '002', 'libelle' => 'Secrétariat'],
            ['code' => '003', 'libelle' => 'Juridique'],
            ['code' => '004', 'libelle' => 'Finance'],
            ['code' => '005', 'libelle' => 'Communication'],
            ['code' => '006', 'libelle' => 'Ressources Humaines'],
            ['code' => '007', 'libelle' => 'Administration Générale'],
        ];

        foreach ($familles as $famille) {
            DB::table('familles_emploi')->updateOrInsert(
                ['code' => $famille['code']],
                array_merge($famille, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
