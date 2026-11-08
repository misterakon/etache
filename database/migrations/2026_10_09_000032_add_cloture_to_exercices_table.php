<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercices', function (Blueprint $table) {
            $table->timestamp('cloture_le')->nullable()->after('mode_avancement');
            $table->foreignId('cloture_par')->nullable()->after('cloture_le')->constrained('users')->nullOnDelete();
            $table->string('cloture_archive')->nullable()->after('cloture_par'); // chemin de l'archive Excel (stockage privé)
        });
    }

    public function down(): void
    {
        Schema::table('exercices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cloture_par');
            $table->dropColumn(['cloture_le', 'cloture_archive']);
        });
    }
};
