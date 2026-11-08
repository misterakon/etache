<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F8 / F12 : synthèses conservées (IA, modèle ou saisie manuelle) et instantanés quotidiens des chiffres de la feuille de route. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syntheses_pilotage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercice_id')->index();
            $table->string('periode_type', 12);               // annee | trimestre | mois
            $table->unsignedTinyInteger('periode_valeur')->nullable();
            $table->string('portee_type', 12)->default('global'); // global | axe | direction
            $table->unsignedBigInteger('portee_id')->nullable();
            $table->longText('contenu');
            $table->string('source', 10);                      // ia | modele | manuel
            $table->string('modele', 60)->nullable();
            $table->string('empreinte', 40)->nullable();
            $table->unsignedBigInteger('auteur_id')->nullable();
            $table->timestamps();
            $table->index(['exercice_id', 'periode_type', 'periode_valeur', 'portee_type', 'portee_id'], 'idx_synthese_cle');
        });

        Schema::create('instantanes_pilotage', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 80)->unique();               // jour|exercice|axe|direction : anti-doublon
            $table->date('jour')->index();
            $table->unsignedBigInteger('exercice_id')->index();
            $table->unsignedBigInteger('axe_id')->nullable();
            $table->unsignedBigInteger('direction_id')->nullable();
            $table->unsignedInteger('taches')->default(0);
            $table->unsignedInteger('terminees')->default(0);
            $table->unsignedInteger('en_cours')->default(0);
            $table->unsignedInteger('en_retard')->default(0);
            $table->unsignedInteger('non_justifies')->default(0);
            $table->decimal('avancement_moyen', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instantanes_pilotage');
        Schema::dropIfExists('syntheses_pilotage');
    }
};
