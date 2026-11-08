<?php

namespace App\Support;

/*
 | Catalogue des droits, organisé comme le menu : menu > sous-menu (page) > niveaux.
 |
 | Pour chaque sous-menu, l'administrateur choisit UN niveau :
 |     Aucun accès  <  Lecture  <  Saisie  <  Gestion
 | Chaque niveau regroupe des droits précis (clés « page.action », ex. direction.creer) : ce sont eux
 | qui sont enregistrés dans le profil (colonne profils_acces.droits) et vérifiés côté serveur, via
 | User::peut('direction.creer') ou Gate::authorize/@can/middleware('can:direction.creer').
 | Quelques actions sensibles (signer, exporter, décider…) sont des « droits spéciaux » à accorder séparément.
 |
 | Le profil marqué « systeme » (ADMIN) a tous les droits d'office, y compris ceux des modules à venir.
 |
 | Ce catalogue COMPLÈTE le système existant (ProfilAcces::slug, User::hasRole(), ExigerProfil) : il ne le
 | remplace pas. Les deux mécanismes cohabitent ; les écrans basculent au fur et à mesure vers le nouveau,
 | un à la fois, sans casser ceux qui n'ont pas encore été convertis.
 |
 | Volontairement laissés HORS de ce catalogue, car leur accès dépend d'une logique métier (hiérarchie,
 | propriétaire de la ressource) et non d'un simple rôle :
 |   - Tâches ponctuelles (chaîne hiérarchique de l'agent : voir App\Support\PonctuellesDroits) ;
 |   - Dossier et carrière d'un agent (l'agent concerné, ou l'administrateur) ;
 |   - Aide, recherche, notifications, mes tâches : ouverts à tout utilisateur connecté.
 */
class Droits
{
    public const NIVEAUX = ['aucun' => 'Aucun accès', 'lecture' => 'Lecture', 'saisie' => 'Saisie', 'gestion' => 'Gestion'];

    /**
     * @return array<string, array{libelle: string, icone: string, pages: array<string, array{libelle: string, a_venir: bool, niveaux: array<string, list<string>>, speciales: array<string, string>}>}>
     */
    public static function catalogue(): array
    {
        return [
            'dgepn' => [
                'libelle' => 'DGEPN (organigramme)',
                'icone' => 'fa-solid fa-sitemap',
                'pages' => [
                    'direction' => self::page('Directions centrales', self::standard('direction')),
                    'sous_direction' => self::page('Sous-directions', self::standard('sous_direction')),
                    'service' => self::page('Services', self::standard('service')),
                    'responsable' => self::page('Responsables (nominations)', ['lecture' => ['responsable.voir'], 'gestion' => ['responsable.voir', 'responsable.creer', 'responsable.fin', 'responsable.supprimer']]),
                    'personnel' => self::page('Personnel (annuaire)', self::standard('personnel'), ['personnel.photo' => "Changer la photo d'un autre agent", 'personnel.dossier' => "Déposer ou retirer un document du dossier d'un agent"]),
                    'fonction' => self::page('Fonctions', self::standard('fonction')),
                ],
            ],
            'feuille_de_route' => [
                'libelle' => 'Feuille de route',
                'icone' => 'fa-solid fa-route',
                'pages' => [
                    'axe' => self::page('Axes stratégiques', self::standard('axe')),
                    'action_cle' => self::page('Actions clés', self::standard('action_cle')),
                    'action_mesurable' => self::page('Actions mesurables', self::standard('action_mesurable')),
                    'tache' => self::page('Liste des tâches', self::standard('tache'), [
                        'tache.cloturer' => 'Valider ou refuser la clôture d’une tâche',
                        'tache.justifier_decision' => 'Décider une demande de justification de retard',
                        'tache.exporter' => 'Exporter la liste des tâches (Excel, PDF)',
                    ]),
                    'import_matrice' => self::page('Importation de la matrice', ['lecture' => ['import_matrice.voir'], 'gestion' => ['import_matrice.voir', 'import_matrice.importer']]),
                    'vues_taches' => self::page('Matrice, Gantt, calendrier', ['lecture' => ['vues_taches.voir']]),
                    'charge_travail' => self::page('Charge de travail', ['lecture' => ['charge_travail.voir']]),
                    'vue_ensemble' => self::page('Vue d’ensemble', ['lecture' => ['vue_ensemble.voir']]),
                    'alerte' => self::page('Alertes (journal)', ['lecture' => ['alerte.voir']]),
                    'justification' => self::page('Demandes de justification', ['lecture' => ['justification.voir']], ['justification.decider' => 'Approuver ou refuser une demande']),
                ],
            ],
            'courrier' => [
                'libelle' => 'Courrier',
                'icone' => 'fa-solid fa-envelope-open-text',
                'pages' => [
                    'courrier_arrivee' => self::page('Courrier arrivée', self::standard('courrier'), ['courrier.exporter' => 'Exporter le registre arrivée']),
                    'courrier_depart' => self::page('Courrier départ', self::standard('depart'), ['depart.exporter' => 'Exporter le registre départ']),
                    'diligences' => self::page('Diligences', ['lecture' => ['diligences.voir']], [
                        'diligences.prendre' => 'Prendre en charge / renvoyer / réimputer',
                        'diligences.solder' => 'Solder ou annuler une diligence',
                        'diligences.action' => 'Gérer le plan de traitement (actions, délais)',
                        'diligences.exporter' => 'Exporter les diligences',
                    ]),
                    'reponse' => self::page('Réponses et visas', ['lecture' => ['reponse.voir']], [
                        'reponse.soumettre' => 'Soumettre ou annuler une réponse',
                        'reponse.viser' => 'Viser ou refuser une réponse (circuit de visa)',
                        'reponse.signer' => 'Signer une réponse (signature réservée)',
                    ]),
                    'archives' => self::page('Archives courrier', ['lecture' => ['archives.voir'], 'gestion' => ['archives.voir', 'archives.classer']]),
                    'parametrage_courrier' => self::page('Paramétrage du courrier', self::standard('parametrage_courrier')),
                ],
            ],
            'pilotage' => [
                'libelle' => 'Pilotage',
                'icone' => 'fa-solid fa-chart-pie',
                'pages' => [
                    'pilotage' => self::page('Vue de pilotage', ['lecture' => ['pilotage.voir']], [
                        'pilotage.exporter' => 'Exporter en Excel et en PDF',
                        'pilotage.synthese' => 'Rédiger la synthèse',
                        'pilotage.corriger_synthese' => 'Corriger une synthèse déjà rédigée',
                    ]),
                    'cockpit' => self::page('Pilotage (cockpit)', ['lecture' => ['cockpit.voir']]),
                ],
            ],
            'personnel_rh' => [
                'libelle' => 'Personnel (RH)',
                'icone' => 'fa-solid fa-users-gear',
                'pages' => [
                    'rh_tableau_de_bord' => self::page('Tableau de bord RH', ['lecture' => ['rh_tableau_de_bord.voir']]),
                    'rh_organigramme' => self::page('Organigramme interactif', ['lecture' => ['rh_organigramme.voir']]),
                    'rh_qualite' => self::page('Qualité des fiches', ['lecture' => ['rh_qualite.voir']], ['rh_qualite.exporter' => 'Exporter']),
                    'rh_doublons' => self::page('Doublons probables', ['lecture' => ['rh_doublons.voir']]),
                    'rh_dossiers' => self::page('Dossiers administratifs (dates)', ['lecture' => ['rh_dossiers.voir'], 'gestion' => ['rh_dossiers.voir', 'rh_dossiers.modifier']]),
                    'rh_comptes' => self::page('Comptes utilisateurs (RH)', ['lecture' => ['rh_comptes.voir'], 'gestion' => ['rh_comptes.voir', 'rh_comptes.creer', 'rh_comptes.desactiver']]),
                    'rh_import' => self::page('Import en masse', ['lecture' => ['rh_import.voir'], 'gestion' => ['rh_import.voir', 'rh_import.importer']]),
                    'rh_archives' => self::page('Agents archivés', ['lecture' => ['rh_archives.voir'], 'gestion' => ['rh_archives.voir', 'rh_archives.archiver', 'rh_archives.restaurer']]),
                    'rh_charge' => self::page('Charge de travail (RH)', ['lecture' => ['rh_charge.voir']]),
                ],
            ],
            'parametres' => [
                'libelle' => 'Paramètres',
                'icone' => 'fa-solid fa-gear',
                'pages' => [
                    'exercice' => self::page('Exercices', ['lecture' => ['exercice.voir'], 'gestion' => ['exercice.voir', 'exercice.creer', 'exercice.modifier', 'exercice.supprimer', 'exercice.cloturer']]),
                    'type_agent' => self::page("Types d'agent", self::standard('type_agent')),
                    'famille_emploi' => self::page("Familles d'emploi", self::standard('famille_emploi')),
                    'emploi' => self::page('Emplois', self::standard('emploi')),
                    'profils_acces' => self::page("Profils d'accès et droits", ['lecture' => ['profils_acces.voir'], 'gestion' => ['profils_acces.voir', 'profils_acces.creer', 'profils_acces.modifier', 'profils_acces.supprimer']]),
                    'role_affectation' => self::page("Rôles d'affectation", self::standard('role_affectation')),
                    'priorite' => self::page('Priorités', self::standard('priorite')),
                    'statut_execution' => self::page("Statuts d'exécution", self::standard('statut_execution')),
                    'situation_temporelle' => self::page('Situations temporelles', self::standard('situation_temporelle')),
                    'canal_notification' => self::page('Canaux de notification', self::standard('canal_notification')),
                    'regle_alerte' => self::page("Règles d'alerte", self::standard('regle_alerte')),
                    'reconduction' => self::page('Reconduction annuelle', ['lecture' => ['reconduction.voir'], 'gestion' => ['reconduction.voir', 'reconduction.executer']]),
                    'comptes_utilisateurs' => self::page('Comptes et accès', ['lecture' => ['comptes_utilisateurs.voir'], 'gestion' => ['comptes_utilisateurs.voir', 'comptes_utilisateurs.creer', 'comptes_utilisateurs.modifier', 'comptes_utilisateurs.supprimer']]),
                    'reglages' => self::page('Réglages de l’application', ['lecture' => ['reglages.voir'], 'gestion' => ['reglages.voir', 'reglages.modifier']], ['reglages.retablir' => 'Rétablir les valeurs par défaut']),
                ],
            ],
            'journal' => [
                'libelle' => "Journal d'activité",
                'icone' => 'fa-solid fa-clock-rotate-left',
                'pages' => [
                    'journal' => self::page("Journal d'activité", ['lecture' => ['journal.voir']]),
                ],
            ],
        ];
    }

    /** @return array<string, array{menu: string, libelle: string, a_venir: bool, niveaux: array<string, list<string>>, speciales: array<string, string>}> */
    public static function pages(): array
    {
        $pages = [];
        foreach (self::catalogue() as $cleMenu => $menu) {
            foreach ($menu['pages'] as $clePage => $page) {
                $pages[$clePage] = ['menu' => $cleMenu] + $page;
            }
        }

        return $pages;
    }

    /** Tous les droits précis. @return list<string> */
    public static function tous(): array
    {
        $cles = [];
        foreach (self::pages() as $page) {
            foreach ($page['niveaux'] as $droits) {
                array_push($cles, ...$droits);
            }
            array_push($cles, ...array_keys($page['speciales']));
        }

        return array_values(array_unique($cles));
    }

    public static function existe(string $droit): bool
    {
        return in_array($droit, self::tous(), true);
    }

    /** Niveau le plus élevé proposé pour une page. */
    public static function niveauMax(array $page): string
    {
        return array_key_last($page['niveaux']);
    }

    /**
     * Choix de l'administrateur  ->  liste des droits précis à enregistrer.
     * Un droit spécial n'est accordé que si la page est accessible (niveau au moins « lecture »).
     *
     * @param  array<string, string>  $niveaux  page => niveau
     * @param  list<string>  $speciales
     * @return list<string>
     */
    public static function depuisChoix(array $niveaux, array $speciales = []): array
    {
        $droits = [];
        foreach (self::pages() as $clePage => $page) {
            $niveau = $niveaux[$clePage] ?? 'aucun';
            if ($niveau === 'aucun' || ! isset($page['niveaux'][$niveau])) {
                continue;
            }
            array_push($droits, ...$page['niveaux'][$niveau]);
            foreach (array_keys($page['speciales']) as $speciale) {
                if (in_array($speciale, $speciales, true)) {
                    $droits[] = $speciale;
                }
            }
        }

        return array_values(array_unique($droits));
    }

    /**
     * Droits précis enregistrés  ->  choix à afficher dans la fenêtre.
     *
     * @param  list<string>  $droits
     * @return array{niveaux: array<string, string>, speciales: list<string>}
     */
    public static function versChoix(array $droits): array
    {
        $niveaux = [];
        $speciales = [];
        foreach (self::pages() as $clePage => $page) {
            $niveau = 'aucun';
            foreach ($page['niveaux'] as $nom => $requis) {
                if (array_diff($requis, $droits) === []) {
                    $niveau = $nom;
                }
            }
            $niveaux[$clePage] = $niveau;
            foreach (array_keys($page['speciales']) as $speciale) {
                if (in_array($speciale, $droits, true)) {
                    $speciales[] = $speciale;
                }
            }
        }

        return ['niveaux' => $niveaux, 'speciales' => $speciales];
    }

    /** Nombre de sous-menus auxquels les droits donnent accès (niveau au moins « lecture »). */
    public static function nombrePagesAccessibles(array $droits): int
    {
        return count(array_filter(self::versChoix($droits)['niveaux'], fn ($n) => $n !== 'aucun'));
    }

    /** Lecture : voir. Saisie : voir, créer, modifier. Gestion : tout, y compris supprimer (ou un autre verbe de retrait). */
    private static function standard(string $page, string $retrait = 'supprimer'): array
    {
        return [
            'lecture' => ["$page.voir"],
            'saisie' => ["$page.voir", "$page.creer", "$page.modifier"],
            'gestion' => ["$page.voir", "$page.creer", "$page.modifier", "$page.$retrait"],
        ];
    }

    private static function page(string $libelle, array $niveaux, array $speciales = [], bool $aVenir = false): array
    {
        return ['libelle' => $libelle, 'a_venir' => $aVenir, 'niveaux' => $niveaux, 'speciales' => $speciales];
    }
}
