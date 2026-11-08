<?php

use App\Models\ProfilAcces;
use App\Support\Droits;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /*
     | Complète les droits de DG et RD pour tous les modules convertis dans cette passe (DGEPN, Utilisateurs,
     | Pilotage, Cockpit, Personnel RH, Feuille de route — Courrier et Tâches ponctuelles ont leur propre
     | système d'autorisation, hors catalogue). Remplace entièrement la liste précédente (elle contenait
     | une erreur : « fonction » avait été accordé à DG alors que ce module est strictement réservé à
     | l'administrateur dans le code, DG en a toujours été exclu).
     |
     | Chaque ligne ci-dessous reflète ce qui est RÉELLEMENT vrai aujourd'hui, vérifié route par route et
     | contrôleur par contrôleur (voir le détail dans le code) :
     | - DG : gestion sur direction/sous-direction/service/les 11 pages de Paramètres/comptes utilisateurs/
     |   axe/action clé/action mesurable/tâche/import de la matrice ; lecture sur responsable/personnel/
     |   pilotage/cockpit/organigramme RH/tableau de bord RH/charge RH/dossiers RH/qualité RH/doublons RH/
     |   archives RH/comptes RH/alerte/justification/journal ; les droits spéciaux de pilotage (export,
     |   rédiger ET corriger la synthèse).
     | - RD : lecture sur responsable/pilotage/cockpit/organigramme RH/justification ; les droits spéciaux
     |   export et rédiger la synthèse (pas « corriger » : réservé à ADMIN et DG dans le code).
     | - fonction, réglages, paramétrage du courrier, import RH, création/désactivation de comptes RH,
     |   archivage/restauration RH : strictement ADMIN, personne d'autre n'en reçoit le droit ici.
     */
    public function up(): void
    {
        if ($dg = ProfilAcces::bySlug(ProfilAcces::DG)) {
            $dg->update(['droits' => Droits::depuisChoix([
                'exercice' => 'gestion', 'type_agent' => 'gestion', 'famille_emploi' => 'gestion', 'emploi' => 'gestion',
                'profils_acces' => 'gestion', 'role_affectation' => 'gestion', 'priorite' => 'gestion', 'statut_execution' => 'gestion',
                'situation_temporelle' => 'gestion', 'canal_notification' => 'gestion', 'regle_alerte' => 'gestion',
                'comptes_utilisateurs' => 'gestion', 'journal' => 'lecture',
                'direction' => 'gestion', 'sous_direction' => 'gestion', 'service' => 'gestion',
                'responsable' => 'lecture', 'personnel' => 'lecture',
                'pilotage' => 'lecture', 'cockpit' => 'lecture', 'rh_organigramme' => 'lecture',
                'rh_tableau_de_bord' => 'lecture', 'rh_charge' => 'lecture', 'rh_dossiers' => 'lecture',
                'rh_qualite' => 'lecture', 'rh_doublons' => 'lecture', 'rh_archives' => 'lecture', 'rh_comptes' => 'lecture',
                'axe' => 'gestion', 'action_cle' => 'gestion', 'action_mesurable' => 'gestion', 'tache' => 'gestion',
                'import_matrice' => 'gestion', 'alerte' => 'lecture', 'justification' => 'lecture',
            ], ['pilotage.exporter', 'pilotage.synthese', 'pilotage.corriger_synthese', 'rh_qualite.exporter'])]);
        }

        if ($rd = ProfilAcces::bySlug(ProfilAcces::RESPONSABLE_DIRECTION)) {
            $rd->update(['droits' => Droits::depuisChoix([
                'responsable' => 'lecture', 'pilotage' => 'lecture', 'cockpit' => 'lecture',
                'rh_organigramme' => 'lecture', 'justification' => 'lecture',
            ], ['pilotage.exporter', 'pilotage.synthese'])]);
        }
    }

    public function down(): void
    {
        // Rien à restaurer : la migration précédente (peuple_droits_profils_existants) a déjà posé une
        // base ; en cas de rollback, on y revient simplement en la rejouant si besoin.
    }
};
