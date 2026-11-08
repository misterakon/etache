<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tache_dependance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained('taches')->cascadeOnDelete();
            $table->foreignId('depend_de_tache_id')->constrained('taches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tache_id', 'depend_de_tache_id'], 'tache_dependance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tache_dependance');
    }
};
