<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilAccesSeeder extends Seeder
{
    public function run(): void
    {
        $profils = [
            ['code' => '001', 'slug' => 'DG', 'libelle' => 'Direction Générale'],
            ['code' => '002', 'slug' => 'RD', 'libelle' => 'Responsable de direction'],
            ['code' => '003', 'slug' => 'RA', 'libelle' => 'Responsable de tâche'],
            ['code' => '004', 'slug' => 'ADMIN', 'libelle' => 'Administrateur'],
        ];

        foreach ($profils as $profil) {
            DB::table('profils_acces')->updateOrInsert(
                ['code' => $profil['code']],
                array_merge($profil, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
