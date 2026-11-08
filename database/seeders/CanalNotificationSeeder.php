<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanalNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $canaux = [
            ['code' => '001', 'libelle' => 'Notification application'],
            ['code' => '002', 'libelle' => 'Email'],
            ['code' => '003', 'libelle' => 'SMS'],
        ];

        foreach ($canaux as $canal) {
            DB::table('canaux_notification')->updateOrInsert(
                ['code' => $canal['code']],
                array_merge($canal, ['actif' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
