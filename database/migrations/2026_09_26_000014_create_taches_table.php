<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_mesurable_id')->constrained('actions_mesurables')->cascadeOnDelete();

            $table->text('libelle');

            // Indicateur / livrable : une valeur par tâche, comme dans la matrice source
            $table->text('indicateur')->nullable();
            $table->text('livrable')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);

            // Responsable(s) et pilote(s) : gérés via la table `affectations` (many-to-many),
            // pas de colonne directe ici.

            // Planification
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable(); // échéance
            $table->unsignedSmallInteger('duree_jours')->nullable();
            $table->foreignId('priorite_id')->nullable()->constrained('priorites')->nullOnDelete();
            $table->decimal('budget_estime', 14, 2)->nullable();

            // Statut d'exécution : saisi/mis à jour par le responsable
            $table->foreignId('statut_execution_id')->constrained('statuts_execution');

            // Situation temporelle : calculée automatiquement par le moteur d'alertes
            $table->foreignId('situation_temporelle_id')->nullable()->constrained('situations_temporelles')->nullOnDelete();

            $table->unsignedTinyInteger('avancement_pourcentage')->default(0);
            $table->text('observations')->nullable();

            // Utilisé par le détecteur d'inactivité (section 9 du cahier des charges)
            $table->timestamp('derniere_mise_a_jour_le')->nullable();

            $table->timestamps();

            $table->index('date_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
