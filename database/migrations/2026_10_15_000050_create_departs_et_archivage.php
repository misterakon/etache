<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** F9 étape 4 : registre départ (un numéro par destinataire), archivage, alertes des diligences. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departs', function (Blueprint $table) {
            $table->id();
            $table->string('groupe', 36)->index(); // même envoi, plusieurs destinataires
            $table->string('numero', 40)->unique();
            $table->unsignedSmallInteger('annee');
            $table->unsignedInteger('sequence');
            $table->unsignedBigInteger('courrier_id')->nullable()->index(); // courrier arrivée d'origine
            $table->unsignedBigInteger('reponse_id')->nullable()->index();
            $table->unsignedBigInteger('diligence_id')->nullable();
            $table->string('destinataire');
            $table->string('destinataire_precision')->nullable();
            $table->string('objet', 500);
            $table->date('date_depart');
            $table->string('mode_envoi', 40)->nullable();
            $table->string('mode_envoi_precision', 150)->nullable();
            $table->unsignedSmallInteger('nombre_pieces')->default(0);
            $table->string('confidentialite', 20)->default('normal');
            $table->text('observations')->nullable();
            $table->string('numero_archives', 60)->nullable();
            $table->date('a_conserver_jusqu_au')->nullable();
            $table->timestamp('archive_le')->nullable();
            $table->unsignedBigInteger('archive_par')->nullable();
            $table->string('statut', 20)->default('enregistre')->index(); // enregistre | archive (Classé)
            $table->unsignedBigInteger('enregistre_par')->nullable();
            $table->timestamps();
            $table->unique(['annee', 'sequence']);
        });

        Schema::table('courriers', function (Blueprint $table) {
            if (! Schema::hasColumn('courriers', 'numero_archives')) {
                $table->string('numero_archives', 60)->nullable();
            }
            if (! Schema::hasColumn('courriers', 'archive_par')) {
                $table->unsignedBigInteger('archive_par')->nullable();
            }
        });

        Schema::create('diligence_alertes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diligence_id');
            $table->string('type', 20); // echeance | retard
            $table->string('cle', 40);
            $table->timestamp('envoye_le')->nullable();
            $table->unique(['diligence_id', 'type', 'cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diligence_alertes');
        Schema::table('courriers', function (Blueprint $table) {
            foreach (['numero_archives', 'archive_par'] as $colonne) {
                if (Schema::hasColumn('courriers', $colonne)) {
                    $table->dropColumn($colonne);
                }
            }
        });
        Schema::dropIfExists('departs');
    }
};
