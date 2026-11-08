<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Validation de la clôture d'une tâche par le supérieur hiérarchique (réglage « Clôture des tâches »). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->string('cloture_validation', 12)->nullable()->index();   // null | a_valider | validee | refusee
            $table->timestamp('cloture_soumise_le')->nullable();             // date de soumission : sert au calcul du retard
            $table->unsignedBigInteger('cloture_soumise_par')->nullable();
            $table->timestamp('cloture_validee_le')->nullable();             // décision (validation ou refus)
            $table->unsignedBigInteger('cloture_validee_par')->nullable();
            $table->text('cloture_motif_refus')->nullable();
            $table->timestamp('cloture_relance_le')->nullable();             // dernière relance au valideur
        });
    }

    public function down(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->dropIndex(['cloture_validation']);
            $table->dropColumn(['cloture_validation', 'cloture_soumise_le', 'cloture_soumise_par', 'cloture_validee_le', 'cloture_validee_par', 'cloture_motif_refus', 'cloture_relance_le']);
        });
    }
};
