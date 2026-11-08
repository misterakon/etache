<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courriers', function (Blueprint $table) {
            $table->id();
            $table->string('sens', 10)->default('arrivee');            // arrivee | depart (le départ vient à l'étape 4)
            $table->string('numero', 40)->unique();                    // ARR-2026-0001
            $table->unsignedSmallInteger('annee');
            $table->unsignedInteger('sequence');
            $table->date('date_reception');
            $table->date('date_courrier')->nullable();
            $table->string('reference_courrier', 100)->nullable();
            $table->string('expediteur');
            $table->string('objet', 500);
            $table->foreignId('type_courrier_id')->constrained('types_courrier');
            $table->foreignId('urgence_id')->constrained('urgences_courrier');
            $table->string('confidentialite', 15)->default('normal');  // normal | confidentiel
            $table->string('origine', 20)->default('service_courrier'); // service_courrier | secretariat_dg
            $table->text('observations')->nullable();
            $table->string('statut', 20)->default('enregistre');
            $table->foreignId('enregistre_par')->nullable()->constrained('users')->nullOnDelete();
            $table->date('a_conserver_jusqu_au')->nullable();
            $table->timestamp('archive_le')->nullable();
            $table->timestamps();

            $table->unique(['sens', 'annee', 'sequence']);
            $table->index('date_reception');
            $table->index('statut');
        });

        // Compteur par sens et par année : verrouillé à chaque émission, donc ni doublon ni trou
        Schema::create('courrier_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('sens', 10);
            $table->unsignedSmallInteger('annee');
            $table->unsignedInteger('dernier')->default(0);
            $table->unique(['sens', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courrier_sequences');
        Schema::dropIfExists('courriers');
    }
};
