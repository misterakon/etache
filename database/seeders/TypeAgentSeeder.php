<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeAgentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => '001', 'libelle' => 'Fonctionnaire'],
            ['code' => '002', 'libelle' => 'Contractuel'],
            ['code' => '003', 'libelle' => 'Stagiaire'],
            ['code' => '004', 'libelle' => 'Consultant'],
        ];

        foreach ($types as $type) {
            DB::table('types_agent')->updateOrInsert(
                ['code' => $type['code']],
                array_merge($type, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
