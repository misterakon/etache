<?php

use App\Models\ProfilAcces;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Nouveau profil : service courrier et secrétariat du DG (enregistrement des courriers)
        ProfilAcces::firstOrCreate(['slug' => 'COURRIER'], ['libelle' => 'Service courrier', 'actif' => true]);
    }

    public function down(): void
    {
        ProfilAcces::where('slug', 'COURRIER')->delete();
    }
};
