<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regles_alerte', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: J-30, J-15, J-1, J+7
            $table->string('libelle');
            // Décalage par rapport à l'échéance : négatif = avant, positif = après
            $table->integer('decalage_jours');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regles_alerte');
    }
};
