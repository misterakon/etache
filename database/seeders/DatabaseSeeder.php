<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Tables de paramètres — à peupler avant toute donnée métier
            StatutExecutionSeeder::class,
            SituationTemporelleSeeder::class,
            PrioriteSeeder::class,
            CanalNotificationSeeder::class,
            ProfilAccesSeeder::class,
            RoleAffectationSeeder::class,
            TypeAgentSeeder::class,
            FamilleEmploiSeeder::class,
        ]);
    }
}
