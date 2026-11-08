<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Instructions du Directeur Général (cases de la fiche de transmission), modifiables par l'ADMIN
        Schema::create('instructions_courrier', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 100)->unique();
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
        foreach (['Pour instruction', 'Me représenter', 'Allocution à préparer', 'Participation / Invitation', "M'en parler", 'Courrier réponse à rédiger', 'Nécessaire à faire', 'Me voir', 'Accord', 'Pour information', 'Pour suivi', 'Archiver'] as $i => $libelle) {
            DB::table('instructions_courrier')->insert(['libelle' => $libelle, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        // Délais de traitement fixés par le DG : Urgent, 24 h, 48 h, Autre (date libre)
        Schema::create('delais_courrier', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('libelle', 60)->unique();
            $table->unsignedSmallInteger('delai_jours')->nullable();   // null quand la date est saisie à la main
            $table->boolean('saisie_libre')->default(false);
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
        foreach ([['URGENT', 'Urgent', 1, false], ['H24', '24 h', 1, false], ['H48', '48 h', 2, false], ['AUTRE', 'Autre', null, true]] as $i => [$code, $libelle, $jours, $libre]) {
            DB::table('delais_courrier')->insert(['code' => $code, 'libelle' => $libelle, 'delai_jours' => $jours, 'saisie_libre' => $libre, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        // Postes destinataires qui ne sont ni une direction ni un service (DGA, chargé d'études, service courrier...)
        Schema::create('postes_imputation', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 120)->unique();
            $table->foreignId('personnel_id')->nullable()->constrained('personnel')->nullOnDelete(); // titulaire actuel du poste
            $table->boolean('peut_reimputer')->default(false);
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
        foreach ([['Directeur Général Adjoint', true], ['Service courrier', false], ['Chargé d\'études Direction Générale', false]] as $i => [$libelle, $reimpute]) {
            DB::table('postes_imputation')->insert(['libelle' => $libelle, 'peut_reimputer' => $reimpute, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('postes_imputation');
        Schema::dropIfExists('delais_courrier');
        Schema::dropIfExists('instructions_courrier');
    }
};
