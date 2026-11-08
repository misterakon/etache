<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Lien vers l'annuaire RH : nullable, un compte peut exister sans fiche personnel (ex: admin technique)
            // unique() : un agent du personnel ne peut avoir qu'un seul compte de connexion
            $table->foreignId('personnel_id')
                ->nullable()
                ->unique()
                ->after('email')
                ->constrained('personnel')
                ->nullOnDelete();

            $table->foreignId('profil_acces_id')
                ->nullable()
                ->after('personnel_id')
                ->constrained('profils_acces')
                ->nullOnDelete();

            $table->boolean('actif')->default(true)->after('profil_acces_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('personnel_id');
            $table->dropConstrainedForeignId('profil_acces_id');
            $table->dropColumn('actif');
        });
    }
};
