<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioriteSeeder extends Seeder
{
    public function run(): void
    {
        $priorites = [
            ['code' => '001', 'libelle' => 'Haute', 'couleur' => '#A73B34'],
            ['code' => '002', 'libelle' => 'Moyenne', 'couleur' => '#B8790F'],
            ['code' => '003', 'libelle' => 'Basse', 'couleur' => '#5B6B84'],
        ];

        foreach ($priorites as $priorite) {
            DB::table('priorites')->updateOrInsert(
                ['code' => $priorite['code']],
                array_merge($priorite, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
