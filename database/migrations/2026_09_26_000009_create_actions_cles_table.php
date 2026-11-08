<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actions_cles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('axe_id')->constrained('axes')->cascadeOnDelete();
            $table->foreignId('direction_id')->nullable()->constrained('directions')->nullOnDelete();
            $table->string('code')->nullable(); // ex: R06, R07
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->text('libelle');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions_cles');
    }
};
