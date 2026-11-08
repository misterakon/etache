<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actions_mesurables', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id'); // ex: AM00001
        });

        Schema::table('taches', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id'); // ex: TA000001
        });
    }

    public function down(): void
    {
        Schema::table('actions_mesurables', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });

        Schema::table('taches', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
