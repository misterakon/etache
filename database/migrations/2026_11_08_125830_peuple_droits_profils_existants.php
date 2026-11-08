<?php

use App\Models\ProfilAcces;
use App\Support\Droits;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /*
     | Attribue à DG et RD les droits précis correspondant à ce qu'ils ont RÉELLEMENT aujourd'hui, d'après
     | les contrôles ExigerProfil déjà en place dans routes/web.php (migration de données, pas de schéma) :
     |
     | - DG : accès complet (gestion) aux pages déjà converties au nouveau système — tout le module
     |   Paramètres, Réglages, Journal, Utilisateurs/Profils, DGEPN (direction/sous-direction/service/
     |   fonction) — et lecture sur Pilotage/Cockpit (ADMIN, DG, RD à égalité sur ces deux pages).
     | - RD : lecture sur Pilotage/Cockpit/Responsables/Organigramme RH, comme aujourd'hui (ADMIN, DG, RD).
     |
     | RA, COURRIER et AGENT ne reçoivent rien ici : leurs pages (Feuille de route, Courrier, Tâches
     | ponctuelles) ne sont pas encore gérées par ce catalogue — elles restent ouvertes à tout utilisateur
     | connecté, comme aujourd'hui, avec leurs propres règles métier (hiérarchie, propriétaire). Elles
     | recevront des droits précis quand ces modules basculeront à leur tour.
     */
    public function up(): void
    {
        if ($dg = ProfilAcces::bySlug(ProfilAcces::DG)) {
            $dg->update(['droits' => Droits::depuisChoix([
                'exercice' => 'gestion', 'type_agent' => 'gestion', 'famille_emploi' => 'gestion', 'emploi' => 'gestion',
                'profils_acces' => 'gestion', 'role_affectation' => 'gestion', 'priorite' => 'gestion', 'statut_execution' => 'gestion',
                'situation_temporelle' => 'gestion', 'canal_notification' => 'gestion', 'regle_alerte' => 'gestion',
                'comptes_utilisateurs' => 'gestion', 'journal' => 'lecture',
                'direction' => 'gestion', 'sous_direction' => 'gestion', 'service' => 'gestion', 'fonction' => 'gestion',
                'responsable' => 'lecture', 'personnel' => 'lecture',
                'pilotage' => 'lecture', 'cockpit' => 'lecture', 'rh_organigramme' => 'lecture',
            ], ['pilotage.exporter', 'pilotage.synthese'])]);
        }

        if ($rd = ProfilAcces::bySlug(ProfilAcces::RESPONSABLE_DIRECTION)) {
            $rd->update(['droits' => Droits::depuisChoix([
                'responsable' => 'lecture', 'pilotage' => 'lecture', 'cockpit' => 'lecture', 'rh_organigramme' => 'lecture',
            ], ['pilotage.exporter', 'pilotage.synthese'])]);
        }
    }

    public function down(): void
    {
        ProfilAcces::bySlug(ProfilAcces::DG)?->update(['droits' => []]);
        ProfilAcces::bySlug(ProfilAcces::RESPONSABLE_DIRECTION)?->update(['droits' => []]);
    }
};
