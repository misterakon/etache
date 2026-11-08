<?php

namespace App\Http\Controllers;

use App\Support\Aide\ManuelAide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manuel d'utilisation intégré : sommaire, chapitres, recherche, manuel complet imprimable, panneau d'aide « ? » de chaque page
 * et visite guidée de la première connexion. Ouvert à tout compte connecté ; chacun ne voit que les chapitres de son profil.
 */
class AideController extends Controller
{
    /** Rubriques du menu et leurs pages : [rubrique => [[libellé, nom de la route]]]. Sert à l'index « Retrouver une page du menu ». */
    private const MENU = [
        'DGEPN' => [['Directions centrales', 'dgepn.direction'], ['Sous-directions', 'dgepn.sous_direction'], ['Services', 'dgepn.service'], ['Personnels', 'dgepn.personnel'], ['Organigramme', 'dgepn.responsable'],
            ['Organigramme interactif', 'rh.organigramme'], ['Tableau de bord RH', 'rh.tableau_de_bord'], ['Qualité des fiches', 'rh.qualite'], ['Doublons', 'rh.doublons'], ['Comptes des agents', 'rh.comptes'],
            ['Import / export Excel', 'rh.import'], ['Charge de travail', 'rh.charge'], ['Validité des dossiers', 'rh.dossiers'], ['Agents archivés', 'rh.archives']],
        'Courrier' => [['Mes courriers à traiter', 'courrier.mes'], ['Registre - Arrivée', 'courrier.arrivee'], ['Tableau des diligences', 'courrier.diligences'], ['Registre - Départ', 'courrier.depart'], ['Archives', 'courrier.archives'], ['Paramétrage', 'courrier.parametrage']],
        'Feuille de route' => [['Importation matrice', 'feuille_de_route.import_matrice.index'], ['Axes', 'feuille_de_route.axe'], ['Actions clés', 'feuille_de_route.action_cle'], ['Actions mesurables', 'feuille_de_route.action_mesurable'],
            ['Liste des tâches', 'feuille_de_route.tache'], ['Tableau matrice', 'feuille_de_route.tache.matrice'], ['Calendrier', 'feuille_de_route.tache.calendrier'], ['Diagramme de Gantt', 'feuille_de_route.tache.gantt'],
            ['Mes tâches', 'feuille_de_route.tache_mes_taches'], ['Tâches ponctuelles', 'ponctuelle.index'], ['Alertes', 'feuille_de_route.alerte'], ['Demandes', 'feuille_de_route.justification.index'], ["Journal d'activité", 'journal.index']],
        'Pilotage' => [['Pilotage', 'cockpit.index']],
        'Utilisateurs' => [["Profil d'accès", 'parametre.profil_acces'], ['Comptes utilisateurs', 'utilisateur.compte']],
        'Paramètres' => [['Exercice', 'parametre.exercice'], ['Réglages', 'reglages.index'], ['Type agent', 'parametre.type_agent'], ['Fonctions', 'dgepn.fonction'], ["Famille d'emploi", 'parametre.famille_emploi'], ['Emploi', 'parametre.emploi'],
            ["Rôle d'affectation", 'parametre.role_affectation'], ['Priorité', 'parametre.priorite'], ["Statut d'exécution", 'parametre.statut_execution'], ['Situation temporelle', 'parametre.situation_temporelle'],
            ['Canal de notification', 'parametre.canal_notification'], ["Règle d'alerte", 'parametre.regle_alerte']],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        $menu = [];
        foreach (self::MENU as $rubrique => $pages) {
            $liens = [];
            foreach ($pages as [$libelle, $route]) {
                $c = ManuelAide::pour($route, $user);
                if ($c) {
                    $liens[] = ['libelle' => $libelle, 'url' => route('aide.chapitre', $c['slug']).($c['section_ancre'] ? '#'.$c['section_ancre'] : '')];
                }
            }
            if ($liens) {
                $menu[$rubrique] = $liens;
            }
        }

        return view('aide.index', ['chapitres' => ManuelAide::visibles($user), 'menu' => $menu]);
    }

    public function chapitre(Request $request, string $slug): View
    {
        $chapitre = ManuelAide::chapitre($slug, $request->user());
        abort_unless($chapitre, 404);
        $tous = array_values(ManuelAide::visibles($request->user()));
        $i = collect($tous)->search(fn ($c) => $c['slug'] === $slug);

        return view('aide.chapitre', [
            'chapitre' => $chapitre, 'chapitres' => $tous,
            'precedent' => $i > 0 ? $tous[$i - 1] : null, 'suivant' => $i !== false && isset($tous[$i + 1]) ? $tous[$i + 1] : null,
        ]);
    }

    /** Tous les chapitres sur une seule page, prête à imprimer ou à enregistrer en PDF depuis le navigateur. */
    public function manuel(Request $request): View
    {
        return view('aide.manuel', ['chapitres' => ManuelAide::visibles($request->user())]);
    }

    public function chercher(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');

        return response()->json(['resultats' => array_map(
            fn ($r) => $r + ['url' => route('aide.chapitre', $r['slug']).'#'.$r['ancre']],
            ManuelAide::chercher($q, $request->user())
        )]);
    }

    /** Contenu du panneau « ? » : le chapitre qui explique la page où l'on se trouve (sinon le sommaire). */
    public function contexte(Request $request): View
    {
        $nom = preg_match('/^[a-z0-9_.-]{1,80}$/i', (string) $request->query('page')) ? (string) $request->query('page') : null;

        return view('aide._panneau', ['chapitre' => ManuelAide::pour($nom, $request->user()), 'chapitres' => ManuelAide::visibles($request->user())]);
    }

    /** La visite guidée est terminée ou ignorée : elle ne se relance plus toute seule. */
    public function visiteTerminee(Request $request): JsonResponse
    {
        $request->user()->forceFill(['visite_guidee_le' => now()])->save();

        return response()->json(['ok' => true]);
    }
}
