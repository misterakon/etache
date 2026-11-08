<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Champs de la « fiche de transmission du courrier » absents de l'étape 1
        Schema::table('courriers', function (Blueprint $table) {
            $table->string('mode_transmission', 25)->nullable()->after('origine');          // porteur_coursier | courrier_electronique | autre
            $table->string('mode_transmission_precision', 150)->nullable()->after('mode_transmission');
            $table->unsignedSmallInteger('nombre_pieces')->default(0)->after('mode_transmission_precision'); // pièces jointes (annexes)
        });
    }

    public function down(): void
    {
        Schema::table('courriers', function (Blueprint $table) {
            $table->dropColumn(['mode_transmission', 'mode_transmission_precision', 'nombre_pieces']);
        });
    }
};
