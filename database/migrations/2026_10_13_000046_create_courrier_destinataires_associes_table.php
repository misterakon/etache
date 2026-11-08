<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('postes_imputation', function (Blueprint $table) {
            $table->string('niveau', 30)->nullable()->after('libelle');
            $table->string('unite_type', 30)->nullable()->after('niveau');
            $table->unsignedBigInteger('unite_id')->nullable()->after('unite_type');
            $table->index(['niveau', 'unite_type', 'unite_id'], 'postes_imputation_position_index');
        });
        DB::table('postes_imputation')->where('libelle', 'Directeur Général Adjoint')->update([
            'niveau' => 'dga', 'unite_type' => null, 'unite_id' => null,
        ]);
        if (! DB::table('postes_imputation')->where('niveau', 'direction_generale')->exists()) {
            DB::table('postes_imputation')->insert([
                'libelle' => 'Directeur Général', 'niveau' => 'direction_generale', 'unite_type' => null, 'unite_id' => null,
                'personnel_id' => null, 'peut_reimputer' => true, 'ordre' => 1, 'actif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        Schema::table('imputations', function (Blueprint $table) {
            $table->foreign('parent_diligence_id', 'imputations_parent_diligence_fk')
                ->references('id')->on('diligences')->nullOnDelete();
        });

        Schema::table('diligences', function (Blueprint $table) {
            $table->foreign('parent_diligence_id', 'diligences_parent_diligence_fk')
                ->references('id')->on('diligences')->nullOnDelete();
            $table->index('parent_diligence_id');
        });

        Schema::create('imputation_destinataires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imputation_id')->constrained('imputations')->cascadeOnDelete();
            $table->foreignId('diligence_id')->nullable()->constrained('diligences')->cascadeOnDelete();
            $table->string('destinataire_type', 30);
            $table->unsignedBigInteger('destinataire_id');
            $table->string('destinataire_libelle');
            $table->string('role', 10); // avis | copie
            $table->timestamps();
            $table->index(['destinataire_type', 'destinataire_id'], 'imputation_destinataires_cible_index');
        });

        Schema::create('diligence_avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imputation_destinataire_id')->constrained('imputation_destinataires')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('commentaire');
            $table->timestamps();
            $table->index(['imputation_destinataire_id', 'created_at'], 'diligence_avis_historique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diligence_avis');
        Schema::dropIfExists('imputation_destinataires');

        Schema::table('diligences', function (Blueprint $table) {
            $table->dropForeign('diligences_parent_diligence_fk');
            $table->dropIndex(['parent_diligence_id']);
        });
        Schema::table('imputations', function (Blueprint $table) {
            $table->dropForeign('imputations_parent_diligence_fk');
        });
        DB::table('postes_imputation')->where('niveau', 'direction_generale')->delete();
        Schema::table('postes_imputation', function (Blueprint $table) {
            $table->dropIndex('postes_imputation_position_index');
            $table->dropColumn(['niveau', 'unite_type', 'unite_id']);
        });
    }
};
