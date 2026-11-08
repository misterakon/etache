<?php

use App\Models\ProfilAcces;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F10.1 : archivage des agents (au lieu de la suppression), mot de passe provisoire, profil d'accès AGENT. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            if (! Schema::hasColumn('personnel', 'archive_le')) {
                $table->timestamp('archive_le')->nullable();
                $table->string('archive_motif', 255)->nullable();
                $table->unsignedBigInteger('archive_par')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'mot_de_passe_provisoire')) {
                $table->boolean('mot_de_passe_provisoire')->default(false);
            }
        });

        // Profil « Agent » : le profil par défaut des comptes créés en masse (aucun droit particulier)
        if (Schema::hasTable('profils_acces') && ! ProfilAcces::where('slug', ProfilAcces::AGENT)->exists()) {
            ProfilAcces::create(['slug' => ProfilAcces::AGENT, 'libelle' => 'Agent', 'actif' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('mot_de_passe_provisoire'));
        Schema::table('personnel', fn (Blueprint $table) => $table->dropColumn(['archive_le', 'archive_motif', 'archive_par']));
    }
};
