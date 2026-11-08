<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');                       // tâche, sous-tâche (puis courrier, personnel...)
            $table->string('categorie', 30)->default('livrable');
            $table->string('nom_original');                     // nom affiché
            $table->string('chemin');                           // stockage privé, nom aléatoire
            $table->string('extension', 10);
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('taille');               // octets
            $table->unsignedSmallInteger('version')->default(1);
            $table->unsignedBigInteger('racine_id')->nullable(); // 1re version du fichier (chaîne des versions)
            $table->boolean('actuelle')->default(true);
            $table->foreignId('depose_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['attachable_type', 'attachable_id', 'actuelle'], 'pj_attachable_actuelle');
            $table->index('racine_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
