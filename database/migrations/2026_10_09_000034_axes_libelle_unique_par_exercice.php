<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le libellé d'un axe est unique PAR EXERCICE (et non plus dans toute la base) :
 * c'est nécessaire pour reprendre les mêmes axes d'une année à l'autre.
 * Si l'ancien index unique sur "libelle" porte un autre nom que 'axes_libelle_unique',
 * le supprimer à la main (SHOW CREATE TABLE axes) avant de lancer la reconduction.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('axes', function (Blueprint $table) {
            if (Schema::hasIndex('axes', 'axes_libelle_unique')) {
                $table->dropUnique('axes_libelle_unique');
            }
            $table->unique(['exercice_id', 'libelle'], 'axes_exercice_libelle_unique');
        });
    }

    public function down(): void
    {
        Schema::table('axes', function (Blueprint $table) {
            $table->dropUnique('axes_exercice_libelle_unique');
        });
    }
};
