<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes_envoyees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained('taches')->cascadeOnDelete();
            $table->foreignId('regle_alerte_id')->constrained('regles_alerte')->cascadeOnDelete();
            $table->foreignId('canal_notification_id')->constrained('canaux_notification')->cascadeOnDelete();
            $table->foreignId('personnel_id')->constrained('personnel')->cascadeOnDelete(); // destinataire
            $table->timestamp('envoye_le');
            $table->timestamps();

            // Empêche de renvoyer deux fois la même alerte, sur le même canal, au même destinataire
            $table->unique(
                ['tache_id', 'regle_alerte_id', 'canal_notification_id', 'personnel_id'],
                'alerte_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes_envoyees');
    }
};
