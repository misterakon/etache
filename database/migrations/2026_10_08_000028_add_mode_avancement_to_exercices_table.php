<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercices', function (Blueprint $table) {
            // 'manuel' (comportement actuel) ou 'sous_taches' (avancement calculé)
            $table->string('mode_avancement', 20)->default('manuel')->after('actif');
        });
    }

    public function down(): void
    {
        Schema::table('exercices', function (Blueprint $table) {
            $table->dropColumn('mode_avancement');
        });
    }
};
