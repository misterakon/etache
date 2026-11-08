<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonnelSeeder extends Seeder
{
    /**
     * 3 agents de test, sans service/type_agent/emploi (a completer plus tard
     * via le formulaire, une fois les vraies directions/sous-directions/services
     * saisis depuis le decret).
     */
    public function run(): void
    {
        $agents = [
            [
                'matricule' => 'TEST-001',
                'nom' => 'Kouassi',
                'prenom' => 'Jean-Baptiste',
                'fonction' => 'Chargé d\'études',
                'email' => 'jb.kouassi@dgepn.ci',
                'telephone' => '+225 07 00 00 00 01',
                'actif' => true,
            ],
            [
                'matricule' => 'TEST-002',
                'nom' => 'Traoré',
                'prenom' => 'Aminata',
                'fonction' => 'Assistante administrative',
                'email' => 'a.traore@dgepn.ci',
                'telephone' => '+225 07 00 00 00 02',
                'actif' => true,
            ],
            [
                'matricule' => 'TEST-003',
                'nom' => 'N\'Guessan',
                'prenom' => 'Yves',
                'fonction' => 'Ingénieur informatique',
                'email' => 'y.nguessan@dgepn.ci',
                'telephone' => '+225 07 00 00 00 03',
                'actif' => true,
            ],
        ];

        foreach ($agents as $agent) {
            DB::table('personnel')->updateOrInsert(
                ['matricule' => $agent['matricule']],
                array_merge($agent, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
