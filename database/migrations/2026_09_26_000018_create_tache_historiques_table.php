<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tache_historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tache_id')->constrained('taches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('champ'); // ex: statut_execution_id, date_fin, avancement_pourcentage...
            $table->text('ancienne_valeur')->nullable();
            $table->text('nouvelle_valeur')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tache_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tache_historiques');
    }
};
