<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F9 étape 3 : réponse à un courrier (projet, circuit de visas, signature scannée). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reponses_courrier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courrier_id')->index();
            $table->unsignedBigInteger('diligence_id')->index();
            $table->string('objet', 500);
            $table->text('commentaire')->nullable();
            $table->string('statut', 20)->default('brouillon')->index(); // brouillon | en_visa | a_corriger | signee | annulee
            $table->unsignedTinyInteger('cycle')->default(1);
            $table->timestamp('soumis_le')->nullable();
            $table->timestamp('signe_le')->nullable();
            $table->unsignedBigInteger('signe_par')->nullable();
            $table->unsignedBigInteger('cree_par')->nullable();
            $table->timestamps();
        });

        Schema::create('reponse_visas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reponse_id')->index();
            $table->unsignedTinyInteger('cycle')->default(1);
            $table->unsignedSmallInteger('ordre');
            $table->unsignedBigInteger('personnel_id')->index();
            $table->string('libelle');
            $table->boolean('signataire')->default(false);
            $table->string('statut', 20)->default('en_attente'); // en_attente | vise | refuse | annule
            $table->unsignedBigInteger('decide_par')->nullable();
            $table->timestamp('decide_le')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reponse_visas');
        Schema::dropIfExists('reponses_courrier');
    }
};
