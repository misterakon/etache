<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actions_mesurables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_cle_id')->constrained('actions_cles')->cascadeOnDelete();
            $table->text('libelle');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions_mesurables');
    }
};
