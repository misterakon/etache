<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regles_alerte', function (Blueprint $table) {
            // Destinataires de l'alerte : liste de cles, ex. ["responsable","pilote","responsable_direction"]
            // (cles definies dans App\Models\RegleAlerte::DESTINATAIRES)
            $table->json('destinataires')->nullable()->after('decalage_jours');
        });

        // Regles deja saisies : valeur par defaut d'apres leur decalage (modifiable ensuite dans le formulaire)
        DB::table('regles_alerte')->whereNull('destinataires')->orderBy('id')->get()->each(function ($regle) {
            $jours = (int) $regle->decalage_jours;

            $destinataires = match (true) {
                $jours <= -30 => ['responsable'],                                              // J-30 : information
                $jours <= 0 => ['responsable', 'pilote'],                                      // J-15 a J0
                $jours < 15 => ['responsable', 'pilote', 'responsable_direction'],             // J+1 a J+14
                default => ['responsable', 'pilote', 'responsable_direction', 'dg'],           // J+15 et au-dela
            };

            DB::table('regles_alerte')->where('id', $regle->id)->update(['destinataires' => json_encode($destinataires)]);
        });
    }

    public function down(): void
    {
        Schema::table('regles_alerte', function (Blueprint $table) {
            $table->dropColumn('destinataires');
        });
    }
};
