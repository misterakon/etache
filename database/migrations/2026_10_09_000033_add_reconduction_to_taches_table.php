<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            // Tâche d'origine (sur la tâche reconduite) et exercice vers lequel une tâche a été reconduite (sur la tâche source)
            $table->foreignId('reconduite_de_id')->nullable()->constrained('taches')->nullOnDelete();
            $table->foreignId('reconduite_vers_exercice_id')->nullable()->constrained('exercices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reconduite_de_id');
            $table->dropConstrainedForeignId('reconduite_vers_exercice_id');
        });
    }
};
