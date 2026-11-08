<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F10.2 : validité des documents du dossier, compétences, formations, alertes RH sans doublon. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossier_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('piece_jointe_id')->unique();
            $table->date('date_document')->nullable();
            $table->date('valide_jusqu_au')->nullable()->index();
            $table->string('observation', 255)->nullable();
            $table->unsignedBigInteger('modifie_par')->nullable();
            $table->timestamps();
        });

        Schema::create('personnel_competences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personnel_id')->index();
            $table->string('libelle', 150);
            $table->unsignedTinyInteger('niveau')->default(2); // 1 débutant, 2 intermédiaire, 3 avancé, 4 expert
            $table->string('commentaire', 255)->nullable();
            $table->unsignedBigInteger('cree_par')->nullable();
            $table->timestamps();
        });

        Schema::create('personnel_formations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personnel_id')->index();
            $table->string('intitule', 200);
            $table->string('organisme', 200)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->unsignedSmallInteger('duree_heures')->nullable();
            $table->boolean('certifiante')->default(false);
            $table->string('observations', 255)->nullable();
            $table->unsignedBigInteger('cree_par')->nullable();
            $table->timestamps();
        });

        Schema::create('rh_alertes', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 120)->unique();
            $table->timestamp('envoye_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_alertes');
        Schema::dropIfExists('personnel_formations');
        Schema::dropIfExists('personnel_competences');
        Schema::dropIfExists('dossier_infos');
    }
};
