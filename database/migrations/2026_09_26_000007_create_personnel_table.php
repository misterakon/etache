<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personnel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('type_agent_id')->nullable()->constrained('types_agent')->nullOnDelete();
            $table->foreignId('emploi_id')->nullable()->constrained('emplois')->nullOnDelete();
            $table->string('matricule')->nullable()->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('fonction')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable(); // utilisé pour les alertes SMS
            $table->string('lieu_habitation')->nullable(); 
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personnel');
    }
};
