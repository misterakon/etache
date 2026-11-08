<?php

use App\Models\ProfilAcces;
use App\Support\Droits;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /*
     | Les pages Axes, Actions clés, Actions mesurables, Liste des tâches, Matrice/Gantt/Calendrier et Vue
     | d'ensemble viennent d'être réellement protégées par un droit côté serveur (Gate::authorize) : avant
     | cette passe, elles étaient ouvertes à tout utilisateur connecté, sans contrôle. RD en avait donc
     | l'usage de fait (même sans droit configuré) ; pour ne pas lui retirer un accès dont il se sert
     | aujourd'hui, on le lui accorde explicitement en lecture, comme le reste de ses droits.
     */
    public function up(): void
    {
        $rd = ProfilAcces::bySlug(ProfilAcces::RESPONSABLE_DIRECTION);
        if (! $rd) {
            return;
        }

        $choix = Droits::versChoix($rd->droits ?? []);
        $niveaux = array_merge($choix['niveaux'], [
            'axe' => 'lecture', 'action_cle' => 'lecture', 'action_mesurable' => 'lecture',
            'tache' => 'lecture', 'vues_taches' => 'lecture', 'vue_ensemble' => 'lecture',
        ]);

        $rd->update(['droits' => Droits::depuisChoix($niveaux, $choix['speciales'])]);
    }

    public function down(): void
    {
        // Rien à restaurer : les migrations précédentes posent déjà une base suffisante en cas de rollback.
    }
};
