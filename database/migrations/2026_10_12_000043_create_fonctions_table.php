<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fonctions : directeur général, DGA, directeur, sous-directeur, chef de service, agent, chargé d'études, conseiller...
        //  - categorie « mandat »             : poste à titulaire unique, tenu par un mandat (titulaire / intérim) sur une unité ;
        //  - categorie « service »            : l'agent est rattaché à un service ;
        //  - categorie « direction_generale » : l'agent est rattaché à la Direction Générale.
        Schema::create('fonctions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('libelle', 100)->unique();
            $table->string('categorie', 20);
            $table->string('niveau_mandat', 30)->nullable();     // direction_generale | dga | direction | sous_direction | service
            $table->boolean('titulaire_unique')->default(false);
            $table->boolean('systeme')->default(false);          // fonctions d'origine : ni supprimables, ni changeables de catégorie
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        $now = now();
        $lignes = [
            ['DG', 'Directeur Général', 'mandat', 'direction_generale', true, true],
            ['DGA', 'Directeur Général Adjoint', 'mandat', 'dga', true, true],
            ['DIRECTEUR', 'Directeur', 'mandat', 'direction', true, true],
            ['SOUS_DIRECTEUR', 'Sous-directeur', 'mandat', 'sous_direction', true, true],
            ['CHEF_SERVICE', 'Chef de service', 'mandat', 'service', true, true],
            ['CHARGE_ETUDES', "Chargé d'études", 'direction_generale', null, false, true],
            ['CONSEILLER', 'Conseiller', 'direction_generale', null, false, true],
            ['AGENT', 'Agent', 'service', null, false, true],
            ['TECH_SURFACE', 'Technicien de surface', 'service', null, false, false],
        ];
        foreach ($lignes as $i => [$code, $libelle, $categorie, $niveau, $unique, $systeme]) {
            DB::table('fonctions')->insert([
                'code' => $code, 'libelle' => $libelle, 'categorie' => $categorie, 'niveau_mandat' => $niveau,
                'titulaire_unique' => $unique, 'systeme' => $systeme, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        Schema::table('personnel', function (Blueprint $table) {
            $table->foreignId('fonction_id')->nullable()->after('emploi_id')->constrained('fonctions')->nullOnDelete();
        });

        // Le niveau d'un mandat devient un texte libre (30 caractères) pour accueillir « dga »
        Schema::table('mandats_responsables', function (Blueprint $table) {
            $table->string('niveau', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fonction_id');
        });
        Schema::dropIfExists('fonctions');
    }
};
