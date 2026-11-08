<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F4b : agent concerné, modalités, conditions et circuit de validation des sous-tâches. Les lignes existantes restent « validées ». */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sous_taches', function (Blueprint $table) {
            $table->unsignedBigInteger('personnel_id')->nullable()->after('tache_id');
            $table->text('modalites')->nullable();
            $table->text('conditions')->nullable();
            $table->string('statut_validation', 20)->default('validee')->index(); // a_valider | validee | refusee
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->text('motif_refus')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sous_taches', function (Blueprint $table) {
            $table->dropColumn(['personnel_id', 'modalites', 'conditions', 'statut_validation', 'valide_par', 'valide_le', 'motif_refus']);
        });
    }
};
