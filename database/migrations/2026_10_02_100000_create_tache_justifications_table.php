<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tache_justifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained('taches')->cascadeOnDelete();
            $table->foreignId('demandeur_id')->constrained('users');
            $table->string('type', 20);                       // justification | report
            $table->string('motif', 40);
            $table->text('commentaire');
            $table->date('echeance_actuelle')->nullable();
            $table->date('echeance_proposee')->nullable();    // report uniquement
            $table->string('statut', 20)->default('en_attente'); // en_attente | approuvee | refusee | annulee
            $table->string('niveau_decision', 5);             // RD | DG (l'ADMIN decide dans tous les cas)
            $table->foreignId('decideur_personnel_id')->nullable()->constrained('personnel')->nullOnDelete();
            $table->foreignId('decide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_commentaire')->nullable();
            $table->timestamp('decide_le')->nullable();
            $table->timestamps();

            $table->index(['tache_id', 'statut']);
            $table->index(['statut', 'niveau_decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tache_justifications');
    }
};
