<?php

use App\Models\ProfilAcces;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     | Ajoute la gestion fine des droits (façon TPI) à côté du système de profils existant :
     | - `droits` (JSON) : les droits précis accordés au profil (App\Support\Droits) ;
     | - `systeme` (bool) : vrai pour le profil Admin — tous les droits d'office, non modifiable, non supprimable.
     | N'enlève rien à l'existant : `code`, `slug`, `libelle`, `actif` restent inchangés et continuent de
     | fonctionner avec User::hasRole() / ExigerProfil, le temps que chaque écran bascule vers le nouveau système.
     */
    public function up(): void
    {
        Schema::table('profils_acces', function (Blueprint $table) {
            $table->json('droits')->nullable()->after('libelle');
            $table->boolean('systeme')->default(false)->after('droits');
        });

        ProfilAcces::where('slug', ProfilAcces::ADMINISTRATEUR)->update(['systeme' => true, 'droits' => json_encode([])]);
    }

    public function down(): void
    {
        Schema::table('profils_acces', function (Blueprint $table) {
            $table->dropColumn(['droits', 'systeme']);
        });
    }
};
