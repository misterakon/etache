<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->foreignId('genre_id')->nullable()->constrained('listes_valeurs')->nullOnDelete();
            $table->foreignId('civilite_id')->nullable()->constrained('listes_valeurs')->nullOnDelete();
            $table->foreignId('statut_matrimonial_id')->nullable()->constrained('listes_valeurs')->nullOnDelete();
            $table->unsignedTinyInteger('nombre_enfants')->nullable();
            $table->string('nom_pere')->nullable();
            $table->string('nom_mere')->nullable();
            $table->string('telephone_2', 30)->nullable();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes_agent')->nullOnDelete();
            $table->foreignId('echelon_id')->nullable()->constrained('echelons')->nullOnDelete();
            $table->date('date_premiere_prise_service')->nullable();
            $table->date('date_prise_service')->nullable();
            $table->date('date_depart_retraite')->nullable();
            $table->date('date_deces')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            foreach (['genre_id', 'civilite_id', 'statut_matrimonial_id', 'grade_id', 'classe_id', 'echelon_id'] as $colonne) {
                $table->dropConstrainedForeignId($colonne);
            }
            $table->dropColumn(['nombre_enfants', 'nom_pere', 'nom_mere', 'telephone_2', 'date_premiere_prise_service', 'date_prise_service', 'date_depart_retraite', 'date_deces']);
        });
    }
};
