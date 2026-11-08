<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Une règle d'alerte (ex: J-1) peut déclencher plusieurs canaux (notification + email + sms)
        Schema::create('regle_alerte_canal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regle_alerte_id')->constrained('regles_alerte')->cascadeOnDelete();
            $table->foreignId('canal_notification_id')->constrained('canaux_notification')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['regle_alerte_id', 'canal_notification_id'], 'regle_alerte_canal_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regle_alerte_canal');
    }
};
