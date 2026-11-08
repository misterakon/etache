<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rendre sous_direction_id nullable (MySQL: on drop puis on recree la contrainte)
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['sous_direction_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('sous_direction_id')->nullable()->change();
            $table->foreign('sous_direction_id')->references('id')->on('sous_directions')->cascadeOnDelete();

            // Rattachement direct a une direction, sans passer par une sous-direction
            // (ex: "Service Financier et des Moyens Generaux" du decret, Article 27)
            $table->foreignId('direction_id')->nullable()->after('sous_direction_id')
                ->constrained('directions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('direction_id');
            $table->dropForeign(['sous_direction_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('sous_direction_id')->nullable(false)->change();
            $table->foreign('sous_direction_id')->references('id')->on('sous_directions')->cascadeOnDelete();
        });
    }
};
