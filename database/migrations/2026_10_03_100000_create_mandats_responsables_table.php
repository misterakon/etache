<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandats_responsables', function (Blueprint $table) {
            $table->id();

            // Unite dirigee : une seule des 3 cles etrangeres est renseignee ; aucune pour la Direction Generale
            $table->string('niveau', 20); // direction_generale | direction | sous_direction | service
            $table->foreignId('direction_id')->nullable()->constrained('directions')->cascadeOnDelete();
            $table->foreignId('sous_direction_id')->nullable()->constrained('sous_directions')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();

            // Qui occupe le poste, et comment
            $table->foreignId('personnel_id')->constrained('personnel')->restrictOnDelete();
            $table->string('type', 15)->default('titulaire'); // titulaire | interim

            // Periode : date_fin vide = mandat en cours
            $table->date('date_debut');
            $table->date('date_fin')->nullable();

            $table->string('reference_acte')->nullable(); // arrete, decision ou note de nomination
            $table->string('motif_fin')->nullable();      // remplacement, mutation, promotion, fin d'interim...
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['niveau', 'direction_id', 'sous_direction_id', 'service_id', 'date_debut'], 'mandats_unite_index');
            $table->index(['personnel_id', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandats_responsables');
    }
};
