<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ---- Listes simples : genre, civilité, statut matrimonial, types de documents du dossier (sans écran de gestion) ----
        Schema::create('listes_valeurs', function (Blueprint $table) {
            $table->id();
            $table->string('liste', 40);                // genre | civilite | statut_matrimonial | type_document
            $table->string('code', 40)->nullable();
            $table->string('libelle', 120);
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->unique(['liste', 'libelle']);
        });

        $listes = [
            'genre' => ['Masculin', 'Féminin'],
            'civilite' => ['M.', 'Mme', 'Mlle', 'Dr', 'Pr', 'Me'],
            'statut_matrimonial' => ['Célibataire', 'Marié(e)', 'Divorcé(e)', 'Veuf(ve)'],
        ];
        foreach ($listes as $liste => $valeurs) {
            foreach ($valeurs as $i => $libelle) {
                DB::table('listes_valeurs')->insert(['liste' => $liste, 'libelle' => $libelle, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        foreach ([['cv', 'CV'], ['cni', "Carte nationale d'identité"], ['diplome', 'Diplôme'], ['acte_naissance', 'Acte de naissance'], ['attestation', 'Attestation'], ['autre', 'Autre document']] as $i => [$code, $libelle]) {
            DB::table('listes_valeurs')->insert(['liste' => 'type_document', 'code' => $code, 'libelle' => $libelle, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        // ---- Échelle de traitement : grades, classes, échelons et indices ----
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->timestamps();
        });
        Schema::create('classes_agent', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 40)->unique();
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->timestamps();
        });
        Schema::create('echelons', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('numero')->unique();
            $table->timestamps();
        });
        Schema::create('echelle_traitements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes_agent')->cascadeOnDelete();
            $table->foreignId('echelon_id')->constrained('echelons')->cascadeOnDelete();
            $table->unsignedSmallInteger('indice');
            $table->unique(['grade_id', 'classe_id', 'echelon_id']);
        });

        $grades = ['A7', 'A6', 'A5', 'A4', 'A3', 'B3', 'B1', 'C3', 'C2', 'C1', 'D2', 'D1'];
        foreach ($grades as $i => $code) {
            DB::table('grades')->insert(['code' => $code, 'ordre' => $i + 1, 'created_at' => $now, 'updated_at' => $now]);
        }
        $classes = ['2ème classe', '1ère classe', 'Classe principale', 'Classe exceptionnelle'];
        foreach ($classes as $i => $libelle) {
            DB::table('classes_agent')->insert(['libelle' => $libelle, 'ordre' => $i + 1, 'created_at' => $now, 'updated_at' => $now]);
        }
        foreach ([1, 2, 3, 4] as $numero) {
            DB::table('echelons')->insert(['numero' => $numero, 'created_at' => $now, 'updated_at' => $now]);
        }

        // Indices : classe => échelon => valeurs dans l'ordre des grades (null = case vide du tableau officiel)
        $echelle = [
            '2ème classe' => [
                1 => [1960, 1315, 965, 895, 830, 680, 565, 485, 475, 460, 440, 415],
                2 => [2015, 1360, 1005, 940, 870, 730, 595, 510, 485, 470, 450, 420],
                3 => [2090, 1410, 1070, 1000, 915, 785, 625, 535, 495, 480, 460, 425],
                4 => [2135, 1485, 1165, 1100, 990, 840, 640, 560, 515, 490, 470, 435],
            ],
            '1ère classe' => [
                1 => [2170, 1580, 1295, 1220, 1100, 890, 695, 605, 550, 515, 485, 400],
                2 => [2255, 1655, 1390, 1315, 1190, 950, 715, 630, 570, 525, 495, 465],
                3 => [2295, 1765, 1520, 1440, 1255, 1000, 740, 650, 580, 545, 510, 475],
            ],
            'Classe principale' => [
                1 => [2350, 1880, 1680, 1605, 1315, 1085, 815, 705, 625, 580, 535, 495],
                2 => [2350, 1955, 1810, 1735, 1380, 1135, 835, 725, 640, 590, 550, 510],
                3 => [2350, 1980, 1905, 1890, 1575, 1185, 860, 755, 650, 600, 560, 525],
            ],
            'Classe exceptionnelle' => [
                1 => [null, 2055, 2040, 2015, 1735, 1270, 880, 775, 665, 610, 575, 545],
                2 => [null, 2130, 2105, 2065, 1760, 1300, 900, 790, 680, 625, 585, 550],
                3 => [null, 2145, 2115, 2095, 1790, 1325, 925, 805, 695, 640, 590, 555],
            ],
        ];
        $idGrade = DB::table('grades')->pluck('id', 'code');
        $idClasse = DB::table('classes_agent')->pluck('id', 'libelle');
        $idEchelon = DB::table('echelons')->pluck('id', 'numero');
        foreach ($echelle as $classe => $echelons) {
            foreach ($echelons as $numero => $indices) {
                foreach ($grades as $k => $code) {
                    if ($indices[$k] === null) {
                        continue;
                    }
                    DB::table('echelle_traitements')->insert([
                        'grade_id' => $idGrade[$code], 'classe_id' => $idClasse[$classe], 'echelon_id' => $idEchelon[$numero], 'indice' => $indices[$k],
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('echelle_traitements');
        Schema::dropIfExists('echelons');
        Schema::dropIfExists('classes_agent');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('listes_valeurs');
    }
};
