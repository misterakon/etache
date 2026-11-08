<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            // Nom du fichier seul (ex: 5f2c....jpg), stocke dans public/uploads/personnel/
            $table->string('photo')->nullable()->after('lieu_habitation');
        });
    }

    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
