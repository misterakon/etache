<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_activites', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acteur_nom')->nullable();          // nom conservé tel qu'il était à l'époque
            $table->string('module', 30);                      // taches, sous_taches, fichiers, exercices, exports...
            $table->string('evenement', 40);                   // depot, coche, export...
            $table->nullableMorphs('sujet');                   // entité concernée
            $table->nullableMorphs('parent');                  // entité parente (ex. la tâche)
            $table->string('resume', 500);
            $table->json('details')->nullable();               // avant / après (jamais de donnée médicale)
            $table->string('ip', 45)->nullable();

            $table->index(['module', 'evenement']);
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_activites');
    }
};
