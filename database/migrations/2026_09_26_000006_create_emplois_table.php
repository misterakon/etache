<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ex: "Ingénieur informatique option gestion", "Secrétaire de direction"...
        Schema::create('emplois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famille_emploi_id')->constrained('familles_emploi')->cascadeOnDelete();
            $table->string('libelle');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emplois');
    }
};
