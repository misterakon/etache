<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Seules les valeurs MODIFIÉES sont stockées : sans ligne, c'est la valeur par défaut du catalogue (App\Support\Reglages).
        Schema::create('reglages', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 100)->unique();      // module.reglage, ex. fichiers.taille_max_ko
            $table->text('valeur');                    // JSON
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglages');
    }
};
