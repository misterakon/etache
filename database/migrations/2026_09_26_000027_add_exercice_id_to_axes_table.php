<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('axes', function (Blueprint $table) {
            $table->foreignId('exercice_id')
                ->nullable() // nullable le temps de reattacher les axes deja importes
                ->after('id')
                ->constrained('exercices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('axes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exercice_id');
        });
    }
};
