<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Directions ASSOCIEES d'une action clé (la principale reste actions_cles.direction_id).
 * Aucune reprise de données : les actions existantes gardent leur direction principale.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('action_cle_direction')) {
            return;
        }

        Schema::create('action_cle_direction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_cle_id')->constrained('actions_cles')->cascadeOnDelete();
            $table->foreignId('direction_id')->constrained('directions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['action_cle_id', 'direction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_cle_direction');
    }
};
