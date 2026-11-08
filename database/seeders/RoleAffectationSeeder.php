<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAffectationSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['code' => '001', 'libelle' => 'Responsable'],
            ['code' => '002', 'libelle' => 'Pilote (coordination)'],
            ['code' => '003', 'libelle' => 'Contributeur'],
        ];

        foreach ($roles as $role) {
            DB::table('roles_affectation')->updateOrInsert(
                ['code' => $role['code']],
                array_merge($role, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
