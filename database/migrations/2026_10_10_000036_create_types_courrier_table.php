<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_courrier', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('libelle', 100)->unique();
            $table->unsignedSmallInteger('ordre')->default(1);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Types proposés au départ (modifiables par l'ADMIN : Courrier > Paramétrage)
        $now = now();
        foreach ([['LETTRE', 'Lettre'], ['NOTE', 'Note de service'], ['DEMANDE', 'Demande'], ['INVITATION', 'Invitation'], ['RAPPORT', 'Rapport'], ['AUTRE', 'Autre']] as $i => [$code, $libelle]) {
            DB::table('types_courrier')->insert(['code' => $code, 'libelle' => $libelle, 'ordre' => $i + 1, 'actif' => true, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('types_courrier');
    }
};
