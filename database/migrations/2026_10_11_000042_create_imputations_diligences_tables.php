<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un acte d'imputation (par le DG, puis réimputations en cascade à l'étape 3)
        Schema::create('imputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courrier_id')->constrained('courriers')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_diligence_id')->nullable();  // réimputation : diligence d'où elle part (étape 3)
            $table->foreignId('imputee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_imputation');
            $table->foreignId('delai_id')->nullable()->constrained('delais_courrier')->nullOnDelete();
            $table->string('delai_libelle', 60)->nullable();                // conservé tel qu'il était
            $table->date('echeance');
            $table->text('instructions_particulieres')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->index('courrier_id');
        });

        Schema::create('imputation_instruction', function (Blueprint $table) {
            $table->foreignId('imputation_id')->constrained('imputations')->cascadeOnDelete();
            $table->foreignId('instruction_id')->constrained('instructions_courrier')->cascadeOnDelete();
            $table->primary(['imputation_id', 'instruction_id']);
        });

        // Une diligence par destinataire d'une imputation : c'est la tâche du courrier
        Schema::create('diligences', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();                           // DL26-0001
            $table->foreignId('courrier_id')->constrained('courriers')->cascadeOnDelete();
            $table->foreignId('imputation_id')->constrained('imputations')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_diligence_id')->nullable();
            $table->string('destinataire_type', 20);                         // direction | sous_direction | service | poste | agent
            $table->unsignedBigInteger('destinataire_id');
            $table->string('destinataire_libelle');                          // conservé tel qu'il était
            $table->foreignId('responsable_personnel_id')->nullable()->constrained('personnel')->nullOnDelete();
            $table->string('role', 10)->default('principal');                // principal | avis | copie
            $table->date('echeance');
            $table->string('statut', 25)->default('a_prendre_en_charge');    // a_prendre_en_charge | en_cours | solde | annule
            $table->timestamp('pris_en_charge_le')->nullable();
            $table->foreignId('pris_en_charge_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('solde_le')->nullable();
            $table->foreignId('solde_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('commentaire_solde')->nullable();
            $table->timestamp('annule_le')->nullable();
            $table->text('motif_annulation')->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('echeance');
            $table->index('responsable_personnel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diligences');
        Schema::dropIfExists('imputation_instruction');
        Schema::dropIfExists('imputations');
    }
};
