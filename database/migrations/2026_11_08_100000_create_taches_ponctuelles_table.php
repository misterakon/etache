<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tâches ponctuelles : le travail d'un agent qui ne relève ni de la feuille de route ni d'une imputation de courrier.
 * Visibles de toute la chaîne hiérarchique de l'agent (snapshot « superieurs »), comptées dans ses retards et sa charge,
 * mais jamais dans l'avancement ni le budget de la feuille de route.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches_ponctuelles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->string('libelle', 255);
            $table->text('description')->nullable();
            $table->foreignId('priorite_id')->nullable()->constrained('priorites')->nullOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->unsignedTinyInteger('avancement_pourcentage')->default(0);
            $table->string('statut', 20)->default('a_faire');          // a_faire | en_cours | terminee | annulee
            $table->string('validation', 20)->default('aucune');       // aucune | en_attente | validee | refusee
            $table->string('origine', 20)->default('agent');           // agent (créée par l'agent) | responsable (confiée)
            $table->foreignId('responsable_personnel_id')->constrained('personnel')->cascadeOnDelete();
            $table->foreignId('cree_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('direction_id')->nullable()->index();
            $table->json('superieurs')->nullable();                    // ids des responsables de la chaîne hiérarchique à la création
            $table->text('motif_decision')->nullable();
            $table->foreignId('decide_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decide_le')->nullable();
            $table->timestamp('terminee_le')->nullable();
            $table->timestamp('annulee_le')->nullable();
            $table->timestamps();

            $table->index(['responsable_personnel_id', 'statut']);
            $table->index('date_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches_ponctuelles');
    }
};
