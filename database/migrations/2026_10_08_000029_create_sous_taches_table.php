<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sous_taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained('taches')->cascadeOnDelete();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->date('echeance')->nullable();
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('cochee')->default(false);
            $table->timestamp('cochee_le')->nullable();
            $table->foreignId('cochee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('poids')->default(1); // même poids pour l'instant ; pondération future
            $table->timestamps();

            $table->index(['tache_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_taches');
    }
};
