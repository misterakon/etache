<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Service Financier et des Moyens Generaux (decret DGEPN, Article 27) :
     * rattache directement a la Direction Generale, ni direction ni sous-direction.
     */
    public function run(): void
    {
        DB::table('services')->updateOrInsert(
            ['nom' => 'Service Financier et des Moyens Généraux'],
            [
                'sous_direction_id' => null,
                'direction_id' => null,
                'code' => 'SFMG',
                'description' => "Gère le patrimoine mobilier et immobilier, l'entretien du parc automobile, la programmation des besoins en fournitures et équipements de bureau et l'exécution du budget de la DGEPN (Article 27).",
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
