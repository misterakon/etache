<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F9 étape 3 : plan de traitement d'une diligence (actions validées) et demandes de délai. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diligence_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diligence_id')->index();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('personnel_id')->nullable();
            $table->date('echeance')->nullable();
            $table->text('modalites')->nullable();
            $table->text('conditions')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('faite')->default(false);
            $table->timestamp('faite_le')->nullable();
            $table->unsignedBigInteger('faite_par')->nullable();
            $table->string('statut_validation', 20)->default('a_valider')->index(); // a_valider | validee | refusee
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->text('motif_refus')->nullable();
            $table->unsignedBigInteger('cree_par')->nullable();
            $table->timestamps();
        });

        Schema::create('diligence_delais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diligence_id')->index();
            $table->unsignedBigInteger('demande_par')->nullable();
            $table->date('echeance_actuelle');
            $table->date('echeance_souhaitee');
            $table->text('motif');
            $table->string('statut', 20)->default('en_attente')->index(); // en_attente | accorde | refuse
            $table->unsignedBigInteger('decide_par')->nullable();
            $table->timestamp('decide_le')->nullable();
            $table->date('echeance_accordee')->nullable();
            $table->text('motif_decision')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diligence_delais');
        Schema::dropIfExists('diligence_actions');
    }
};
