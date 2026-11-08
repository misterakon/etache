<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urgences_courrier', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('libelle', 100)->unique();
            $table->unsignedSmallInteger('delai_jours');       // délai de traitement proposé à l'imputation (modifiable à chaque imputation)
            $table->string('couleur', 7)->default('#2B6CB0');
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Valeurs par défaut du cahier des charges : à confirmer par la direction, modifiables à tout moment
        $now = now();
        foreach ([['NORMAL', 'Normal', 15, '#2B6CB0'], ['URGENT', 'Urgent', 7, '#C77D0A'], ['TRES_URGENT', 'Très urgent', 2, '#A73B34']] as $i => [$code, $libelle, $delai, $couleur]) {
            DB::table('urgences_courrier')->insert(['code' => $code, 'libelle' => $libelle, 'delai_jours' => $delai, 'couleur' => $couleur, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('urgences_courrier');
    }
};
