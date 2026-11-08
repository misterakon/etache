<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnel')->cascadeOnDelete();
            $table->foreignId('role_affectation_id')->constrained('roles_affectation')->cascadeOnDelete();

            // Relation polymorphique : ActionCle, ActionMesurable ou Tache
            // (voir App\Models\ActionCle::class, ActionMesurable::class, Tache::class au niveau applicatif)
            $table->string('affectable_type');
            $table->unsignedBigInteger('affectable_id');

            $table->timestamps();

            $table->index(['affectable_type', 'affectable_id']);
            $table->unique(
                ['personnel_id', 'role_affectation_id', 'affectable_type', 'affectable_id'],
                'affectation_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
