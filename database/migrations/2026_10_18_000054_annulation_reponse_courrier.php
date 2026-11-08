<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Annulation d'une réponse déjà visée : demande soumise à l'accord du dernier visa (circuit suspendu pendant l'attente). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reponses_courrier', function (Blueprint $table) {
            $table->unsignedBigInteger('annulation_demandee_par')->nullable();   // utilisateur qui demande
            $table->timestamp('annulation_demandee_le')->nullable();             // non nul = demande en attente
            $table->text('annulation_motif')->nullable();
            $table->unsignedBigInteger('annulation_decideur_id')->nullable();    // personnel du dernier visa
        });
    }

    public function down(): void
    {
        Schema::table('reponses_courrier', function (Blueprint $table) {
            $table->dropColumn(['annulation_demandee_par', 'annulation_demandee_le', 'annulation_motif', 'annulation_decideur_id']);
        });
    }
};
