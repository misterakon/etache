<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sous_direction_id')->constrained('sous_directions')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['sous_direction_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
