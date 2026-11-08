<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertes_envoyees', function (Blueprint $table) {
            // Echeance de la tache au moment de l'envoi : apres un report accepte, les rappels peuvent etre rejoues.
            $table->date('echeance_au_envoi')->nullable()->after('personnel_id');
        });

        // Alertes deja envoyees : on suppose l'echeance actuelle de la tache
        DB::table('alertes_envoyees')
            ->join('taches', 'taches.id', '=', 'alertes_envoyees.tache_id')
            ->whereNull('alertes_envoyees.echeance_au_envoi')
            ->update(['alertes_envoyees.echeance_au_envoi' => DB::raw('taches.date_fin')]);

        Schema::table('alertes_envoyees', function (Blueprint $table) {
            // Anti-doublon : une alerte par tache, regle, canal, agent et echeance
            $table->unique(
                ['tache_id', 'regle_alerte_id', 'canal_notification_id', 'personnel_id', 'echeance_au_envoi'],
                'alertes_envoyees_anti_doublon'
            );
        });
    }

    public function down(): void
    {
        Schema::table('alertes_envoyees', function (Blueprint $table) {
            $table->dropUnique('alertes_envoyees_anti_doublon');
            $table->dropColumn('echeance_au_envoi');
        });
    }
};
