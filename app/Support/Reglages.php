<?php

namespace App\Support;

use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Models\Diligence;
use App\Models\Reglage;
use DomainException;
use InvalidArgumentException;
use Throwable;

/**
 * Réglages de l'application : tout ce qui était écrit en dur est ici, modifiable depuis l'écran « Réglages » (ADMIN).
 *
 *   Reglages::get('fichiers.taille_max_ko')                 // valeur courante (ou valeur par défaut)
 *   auth()->user()->hasRole(Reglages::get('exercice.roles_cloture'))
 *
 * - La valeur par défaut reproduit le comportement d'origine : tant que rien n'est modifié, rien ne change.
 * - Seules les valeurs modifiées sont stockées (table reglages). Rétablir = supprimer la ligne.
 * - Chaque modification et chaque rétablissement sont écrits dans le journal d'activité (module « reglages »).
 * - Types : bool, int (min/max), texte, choix (une option), liste (plusieurs options).
 * - Ne se paramètrent jamais : séparation des rôles, journal d'audit (ADMIN reste obligatoire dans les listes de profils).
 */
class Reglages
{
    public const ROLES = [
        'ADMIN' => 'Administrateur',
        'DG' => 'Direction générale',
        'RD' => 'Responsable de direction',
        'RA' => 'Responsable de tâche',
        'COURRIER' => 'Service courrier',
    ];

    private static ?array $surcharges = null;

    /** @return array<string, array{libelle: string, description: string, reglages: array<string, array>}> */
    public static function catalogue(): array
    {
        $extensions = array_keys(config('pieces_jointes.extensions'));
        $optionsExtensions = array_combine($extensions, array_map('strtoupper', $extensions));

        $roles = fn (string $libelle, string $aide, array $defaut, array $obligatoires = ['ADMIN']) => [
            'libelle' => $libelle, 'aide' => $aide, 'type' => 'liste', 'options' => self::ROLES, 'defaut' => $defaut, 'obligatoires' => $obligatoires,
        ];

        $niveaux = ['bloquant' => 'Bloquant : empêche la clôture', 'avertissement' => "Avertissement : n'empêche pas la clôture", 'ignore' => 'Ne pas contrôler'];
        $controle = fn (string $libelle, string $defaut) => [
            'libelle' => 'Contrôle : '.$libelle, 'aide' => "Niveau de ce contrôle avant la clôture d'un exercice.",
            'type' => 'choix', 'options' => $niveaux, 'defaut' => $defaut,
        ];

        return [
            'exercice' => [
                'libelle' => 'Exercice : clôture et reconduction',
                'description' => 'Qui peut clôturer, rouvrir et reconduire, et ce qui est contrôlé avant une clôture.',
                'reglages' => [
                    'roles_cloture' => $roles('Profils pouvant clôturer un exercice', "L'administrateur reste toujours autorisé.", ['ADMIN', 'DG']),
                    'roles_reouverture' => $roles('Profils pouvant rouvrir un exercice clos', "L'administrateur reste toujours autorisé. La réouverture exige un motif et est tracée.", ['ADMIN']),
                    'roles_reconduction' => $roles('Profils pouvant reconduire des tâches ou une structure', "L'administrateur reste toujours autorisé.", ['ADMIN', 'DG']),
                    'controle_justifications_en_attente' => $controle('demandes de justification ou de report en attente', 'bloquant'),
                    'controle_taches_non_terminees' => $controle('tâches non terminées et non annulées', 'avertissement'),
                    'controle_retards_non_justifies' => $controle('tâches en retard non justifiées', 'avertissement'),
                    'controle_taches_sans_responsable' => $controle('tâches en cours sans responsable', 'avertissement'),
                    'controle_sous_taches_non_cochees' => $controle('sous-tâches non cochées', 'avertissement'),
                    'controle_taches_a_valider' => $controle('tâches en attente de validation de clôture', 'avertissement'),
                    'reconduction_affectations' => ['libelle' => 'Reconduction : reprendre les affectations', 'aide' => 'Valeur proposée (cochée) dans la page de reconduction ; elle reste modifiable à chaque reconduction.', 'type' => 'bool', 'defaut' => true],
                    'reconduction_sous_taches' => ['libelle' => 'Reconduction : reprendre les sous-tâches non cochées', 'aide' => 'Valeur proposée dans la page de reconduction.', 'type' => 'bool', 'defaut' => true],
                    'reconduction_echeance_preremplie' => ['libelle' => "Reconduction : pré-remplir l'échéance", 'aide' => "Propose l'échéance actuelle de la tâche quand elle n'est pas antérieure au début du nouvel exercice. La saisie reste obligatoire.", 'type' => 'bool', 'defaut' => true],
                    'reconduction_structure_si_absente' => ['libelle' => "Reconduction : recréer la structure d'origine si elle est absente", 'aide' => "Sans choix d'une action mesurable de destination, la tâche est rattachée à l'axe, l'action clé et l'action mesurable de même libellé, créés s'ils n'existent pas. Désactivé : le choix d'une action mesurable de destination devient obligatoire.", 'type' => 'bool', 'defaut' => true],
                ],
            ],
            'sous_taches' => [
                'libelle' => 'Sous-tâches',
                'description' => 'Qui gère les sous-tâches et dans quelles limites.',
                'reglages' => [
                    'max_par_tache' => ['libelle' => 'Nombre maximal de sous-tâches par tâche', 'aide' => '', 'type' => 'int', 'defaut' => 50, 'min' => 1, 'max' => 500],
                    'roles_gestion' => $roles('Profils pouvant gérer les sous-tâches de toutes les tâches', 'En plus du responsable de chaque tâche, qui gère toujours les siennes.', ['ADMIN', 'DG']),
                    'roles_apres_echeance' => $roles("Profils pouvant modifier les sous-tâches après l'échéance", "Après l'échéance de la tâche, les autres profils (dont le responsable) ne peuvent plus les modifier.", ['ADMIN', 'DG', 'RD']),
                    'statut_automatique' => ['libelle' => 'Statut automatique en mode « par sous-tâches »', 'aide' => 'Aucune cochée = Non démarrée, certaines = En cours, toutes = Terminée. Bloquée, Suspendue et Annulée restent manuelles.', 'type' => 'bool', 'defaut' => true],
                ],
            ],
            'cloture_taches' => [
                'libelle' => 'Clôture des tâches',
                'description' => "Faire valider par le supérieur hiérarchique le passage d'une tâche à « Terminée ». Désactivé : le fonctionnement reste inchangé.",
                'reglages' => [
                    'validation_active' => ['libelle' => "Faire valider la clôture d'une tâche par le supérieur hiérarchique", 'aide' => "Activé : quand la dernière sous-tâche est faite, ou quand le responsable choisit « Terminée », la tâche passe en attente de validation. Elle reste « En cours » (99 %) et n'est pas comptée comme terminée tant que son supérieur (chef de service, sous-directeur, directeur, puis DG) n'a pas validé.", 'type' => 'bool', 'defaut' => false],
                    'relance_jours' => ['libelle' => 'Relancer le valideur après', 'aide' => 'Une relance est envoyée au valideur, puis au DG après le double de ce délai.', 'type' => 'int', 'defaut' => 3, 'min' => 1, 'max' => 30, 'unite' => 'jours'],
                ],
            ],
            'taches_ponctuelles' => [
                'libelle' => 'Tâches ponctuelles',
                'description' => "Le travail d'un agent hors feuille de route et hors courrier. Visible de toute sa chaîne hiérarchique, jamais compté dans l'avancement de la feuille de route.",
                'reglages' => [
                    'controle' => ['libelle' => 'Contrôle des tâches créées par un agent', 'aide' => "Notification : le supérieur est prévenu et peut refuser ou annuler. Validation : la tâche attend l'accord du supérieur et ne compte ni dans les retards ni dans la charge. Aucun : pas d'information.", 'type' => 'choix', 'options' => ['notification' => 'Notification du supérieur (par défaut)', 'validation' => 'Validation préalable du supérieur', 'aucun' => 'Aucun contrôle'], 'defaut' => 'notification'],
                    'max_ouvertes' => ['libelle' => 'Nombre maximal de tâches ponctuelles ouvertes par agent', 'aide' => 'Garde-fou contre la saisie en masse.', 'type' => 'int', 'defaut' => 40, 'min' => 1, 'max' => 500],
                    'alerte_retard' => ['libelle' => "Alerter l'agent et son supérieur en cas de retard", 'aide' => 'Une notification est envoyée une fois, le lendemain de l\'échéance.', 'type' => 'bool', 'defaut' => true],
                ],
            ],
            'fichiers' => [
                'libelle' => 'Fichiers (livrables)',
                'description' => 'Dépôt des fichiers sur les tâches et sous-tâches. La limite réelle dépend aussi de php.ini (upload_max_filesize et post_max_size).',
                'reglages' => [
                    'taille_max_ko' => ['libelle' => "Taille maximale d'un fichier", 'aide' => '', 'type' => 'int', 'defaut' => 10240, 'min' => 100, 'max' => 51200, 'unite' => 'Ko'],
                    'max_par_element' => ['libelle' => 'Nombre maximal de fichiers par tâche et par sous-tâche', 'aide' => '', 'type' => 'int', 'defaut' => 10, 'min' => 1, 'max' => 100],
                    'extensions' => ['libelle' => 'Types de fichiers autorisés', 'aide' => 'Le contenu réel du fichier est vérifié, pas seulement son nom.', 'type' => 'liste', 'options' => $optionsExtensions, 'defaut' => $extensions],
                    'roles_depot' => $roles('Profils pouvant déposer des fichiers sur toutes les tâches', 'En plus du responsable de chaque tâche, qui dépose toujours sur les siennes.', ['ADMIN', 'DG']),
                    'roles_suppression' => $roles('Profils pouvant toujours supprimer un fichier', "Les autres ne suppriment que leurs propres dépôts, avant l'échéance (voir réglage suivant).", ['ADMIN', 'DG', 'RD']),
                    'auteur_supprime_avant_echeance' => ['libelle' => "L'auteur peut supprimer son dépôt avant l'échéance", 'aide' => '', 'type' => 'bool', 'defaut' => true],
                ],
            ],
            'courrier' => [
                'libelle' => 'Courrier',
                'description' => 'Registre des courriers, numérotation et scans. Les urgences, délais et types de courrier se gèrent dans Courrier > Paramétrage.',
                'reglages' => [
                    'roles_enregistrement' => $roles('Profils pouvant enregistrer et modifier un courrier', "L'administrateur reste toujours autorisé. Le profil « Service courrier » sert au service courrier et au secrétariat du DG.", ['ADMIN', 'COURRIER']),
                    'roles_registre' => $roles('Profils pouvant consulter le registre', "Les courriers confidentiels restent réservés à l'ADMIN, au DG et à la personne qui les a enregistrés.", ['ADMIN', 'DG', 'COURRIER']),
                    'format_arrivee' => [
                        'libelle' => 'Format de la numérotation des courriers arrivée',
                        'aide' => "Éléments possibles : {AA} (année sur 2 chiffres), {AAAA} (4 chiffres), {NNNN} (numéro d'ordre sur 4 chiffres ; {NNN} et {NNNNN} existent aussi). Exemple : A{AA}-{NNNN} donne A26-0001. Le numéro repart de 1 chaque année. Le format est VERROUILLÉ dès qu'un numéro a été émis.",
                        'type' => 'texte', 'defaut' => 'A{AA}-{NNNN}', 'max' => 40,
                        'valider' => fn ($v) => (preg_match('/\{N{3,5}\}/', $v) && preg_match('/^[A-Za-z0-9\-_\/{}. ]+$/', $v)) ? null : "le format doit contenir un numéro d'ordre ({NNNN}) et seulement des lettres, chiffres, tirets ou barres.",
                        'verrou' => fn () => Courrier::where('sens', Courrier::SENS_ARRIVEE)->exists(),
                        'verrou_message' => 'Verrouillé : des numéros ont déjà été émis.',
                    ],
                    'format_depart' => [
                        'libelle' => 'Format de la numérotation des courriers départ',
                        'aide' => "Mêmes éléments que pour l'arrivée. Exemple : D{AA}-{NNNN} donne D26-0001. Verrouillé dès qu'un numéro de départ a été émis.",
                        'type' => 'texte', 'defaut' => 'D{AA}-{NNNN}', 'max' => 40,
                        'valider' => fn ($v) => (preg_match('/\{N{3,5}\}/', $v) && preg_match('/^[A-Za-z0-9\-_\/{}. ]+$/', $v)) ? null : "le format doit contenir un numéro d'ordre ({NNNN}) et seulement des lettres, chiffres, tirets ou barres.",
                        'verrou' => fn () => Courrier::where('sens', Courrier::SENS_DEPART)->exists() || CourrierDepart::query()->exists(),
                        'verrou_message' => 'Verrouillé : des numéros ont déjà été émis.',
                    ],
                    'roles_imputation' => $roles('Profils pouvant imputer un courrier', "Le DG impute ; l'administrateur reste toujours autorisé. La réimputation en cascade par les responsables d'unité relève d'une étape suivante.", ['ADMIN', 'DG']),
                    'informer_superieur' => ['libelle' => "Informer le supérieur hiérarchique d'une imputation directe à un agent", 'aide' => "Quand le DG impute directement à un agent, le responsable de son service et celui de sa direction reçoivent une notification d'information.", 'type' => 'bool', 'defaut' => true],
                    'format_diligence' => [
                        'libelle' => 'Format de la numérotation des diligences',
                        'aide' => "Mêmes éléments que pour les courriers. Exemple : DL{AA}-{NNNN} donne DL26-0001. Verrouillé dès qu'une diligence existe.",
                        'type' => 'texte', 'defaut' => 'DL{AA}-{NNNN}', 'max' => 40,
                        'valider' => fn ($v) => (preg_match('/\{N{3,5}\}/', $v) && preg_match('/^[A-Za-z0-9\-_\/{}. ]+$/', $v)) ? null : "le format doit contenir un numéro d'ordre ({NNNN}) et seulement des lettres, chiffres, tirets ou barres.",
                        'verrou' => fn () => Diligence::query()->exists(),
                        'verrou_message' => 'Verrouillé : des diligences existent déjà.',
                    ],
                    'roles_depart' => $roles('Profils pouvant enregistrer un courrier départ et classer les courriers', "L'administrateur reste toujours autorisé. Le DG peut toujours classer.", ['ADMIN', 'COURRIER']),
                    'depart_auto' => ['libelle' => 'Créer automatiquement le courrier départ quand une réponse est signée', 'aide' => "Activé : la signature d'une réponse enregistre aussitôt le courrier départ (adressé à l'expéditeur du courrier d'origine). Désactivé : le service courrier le crée depuis la réponse signée.", 'type' => 'bool', 'defaut' => false],
                    'reimputation_confidentiel_dg_seul' => ['libelle' => "Réserver au DG la réimputation d'un courrier confidentiel", 'aide' => "Activé : seuls le DG et l'administrateur réimputent un courrier confidentiel ; les responsables utilisent « Renvoyer au DG ». Désactivé : un responsable peut réimputer, ce qui étend la lecture du contenu aux nouveaux destinataires.", 'type' => 'bool', 'defaut' => false],
                    'reponse_visa_chaine' => ['libelle' => 'Faire monter la réponse toute la chaîne hiérarchique pour visa', 'aide' => 'Activé : chef de service, sous-directeur, directeur puis Directeur Général (qui signe). Désactivé : le Directeur Général vise et signe seul.', 'type' => 'bool', 'defaut' => true],
                    'conservation_ans' => ['libelle' => "Durée de conservation proposée à l'archivage", 'aide' => "Pré-remplit la date « à conserver jusqu'au ». Aucune suppression automatique n'est jamais effectuée.", 'type' => 'int', 'defaut' => 10, 'min' => 1, 'max' => 100, 'unite' => 'ans'],
                    'alerte_jours_avant' => ['libelle' => "Alerter avant l'échéance d'une diligence", 'aide' => "Nombre de jours avant l'échéance où le responsable reçoit un rappel (0 : le jour même seulement).", 'type' => 'int', 'defaut' => 3, 'min' => 0, 'max' => 30, 'unite' => 'jours'],
                    'alerte_retard_rappel_jours' => ['libelle' => "Rappel en cas de retard d'une diligence", 'aide' => 'Le responsable et son supérieur sont alertés dès le premier jour de retard, puis rappelés à cet intervalle.', 'type' => 'int', 'defaut' => 3, 'min' => 1, 'max' => 30, 'unite' => 'jours'],
                    'fiche_ministere' => ['libelle' => 'En-tête de la fiche de transmission : ministère', 'aide' => '', 'type' => 'texte', 'defaut' => "MINISTERE DU PORTEFEUILLE DE L'ETAT ET DES ENTREPRISES PUBLIQUES", 'max' => 150],
                    'fiche_direction' => ['libelle' => 'En-tête de la fiche de transmission : direction', 'aide' => '', 'type' => 'texte', 'defaut' => 'DIRECTION GENERALE DES ETABLISSEMENTS PUBLICS NATIONAUX', 'max' => 150],
                    'scan_obligatoire' => ['libelle' => "Scan obligatoire à l'enregistrement", 'aide' => 'Désactivé : un courrier peut être enregistré sans scan, qui sera ajouté ensuite.', 'type' => 'bool', 'defaut' => true],
                    'scan_taille_max_ko' => ['libelle' => "Taille maximale d'un scan", 'aide' => '', 'type' => 'int', 'defaut' => 20480, 'min' => 500, 'max' => 102400, 'unite' => 'Ko'],
                    'scan_extensions' => ['libelle' => 'Types de fichiers acceptés pour le scan', 'aide' => 'Le contenu réel du fichier est vérifié.', 'type' => 'liste', 'options' => ['pdf' => 'PDF', 'jpg' => 'JPG', 'jpeg' => 'JPEG', 'png' => 'PNG'], 'defaut' => ['pdf', 'jpg', 'jpeg', 'png']],
                ],
            ],
            'personnel' => [
                'libelle' => 'Dossier du personnel',
                'description' => "Documents du dossier d'un agent (CV, carte d'identité, diplômes…). Consultables par l'administrateur, le DG et l'agent lui-même ; déposés et supprimés par l'administrateur.",
                'reglages' => [
                    'dossier_taille_max_ko' => ['libelle' => "Taille maximale d'un document", 'aide' => '', 'type' => 'int', 'defaut' => 10240, 'min' => 100, 'max' => 51200, 'unite' => 'Ko'],
                    'seuil_surcharge' => ['libelle' => 'Seuil de surcharge de travail', 'aide' => "Nombre d'éléments ouverts (tâches, diligences, actions) à partir duquel un agent est signalé en sur-affectation ; son supérieur est alerté une fois par semaine.", 'type' => 'int', 'defaut' => 8, 'min' => 3, 'max' => 60],
                    'rappel_dossier_jours' => ['libelle' => "Rappel avant la fin de validité d'un document du dossier", 'aide' => "L'agent et l'administrateur sont alertés ce nombre de jours avant l'échéance, puis à l'expiration.", 'type' => 'int', 'defaut' => 30, 'min' => 1, 'max' => 180, 'unite' => 'jours'],
                    'age_retraite' => ['libelle' => 'Âge de départ à la retraite retenu', 'aide' => "Sert aux retraites prévues du tableau de bord quand la date de départ n'est pas renseignée sur la fiche.", 'type' => 'int', 'defaut' => 60, 'min' => 50, 'max' => 70, 'unite' => 'ans'],
                    'dossier_extensions' => ['libelle' => 'Types de fichiers acceptés', 'aide' => 'Le contenu réel du fichier est vérifié.', 'type' => 'liste', 'options' => ['pdf' => 'PDF', 'jpg' => 'JPG', 'jpeg' => 'JPEG', 'png' => 'PNG', 'doc' => 'DOC', 'docx' => 'DOCX'], 'defaut' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']],
                ],
            ],
            'exports' => [
                'libelle' => 'Exports',
                'description' => 'Exports Excel et PDF des tâches.',
                'reglages' => [
                    'jours_a_venir' => ['libelle' => 'Délais proposés pour l\'export « Tâches à venir » (jours)', 'aide' => '', 'type' => 'liste', 'options' => [7 => '7 jours', 15 => '15 jours', 30 => '30 jours', 45 => '45 jours', 60 => '60 jours'], 'defaut' => ['7', '15', '30']],
                    'mention_pdf' => ['libelle' => 'Mention en pied de page des PDF', 'aide' => '', 'type' => 'texte', 'defaut' => 'Document à usage interne — e-Tâche', 'max' => 150],
                ],
            ],
            'synthese' => [
                'libelle' => 'Pilotage et synthèses',
                'description' => "Tableau de pilotage (graphiques) et synthèses. La synthèse par IA n'envoie que des totaux : jamais l'objet d'un courrier, le nom d'un agent ni un document. La clé d'API se renseigne uniquement dans le fichier .env (ANTHROPIC_API_KEY).",
                'reglages' => [
                    'ia_active' => ['libelle' => 'Rédiger la synthèse avec l\'IA', 'aide' => "Désactivé (par défaut) : synthèse par modèles de phrases, sans aucun appel externe. Activé : la synthèse est rédigée par Claude à partir des totaux ; en cas d'échec ou de plafond atteint, le repli est automatique.", 'type' => 'bool', 'defaut' => false],
                    'modele' => ['libelle' => "Modèle d'IA", 'aide' => 'Sonnet : meilleure rédaction. Haiku : plus économique.', 'type' => 'choix', 'options' => ['claude-sonnet-5-5' => 'Claude Sonnet 5.5 (qualité)', 'claude-haiku-5-5' => 'Claude Haiku 5.5 (économique)'], 'defaut' => 'claude-sonnet-5-5'],
                    'generations_par_jour' => ['libelle' => 'Synthèses IA maximum par jour', 'aide' => "Plafond de dépense : au-delà, la synthèse passe par les modèles de phrases jusqu'au lendemain.", 'type' => 'int', 'defaut' => 5, 'min' => 1, 'max' => 100],
                    'auto_hebdo' => ['libelle' => 'Générer une synthèse chaque lundi matin', 'aide' => 'Génère la synthèse de toute la structure le lundi à 07:30 (le planificateur doit tourner).', 'type' => 'bool', 'defaut' => false],
                ],
            ],
            'securite' => [
                'libelle' => 'Sécurité de la session',
                'description' => "Verrouillage automatique de l'écran après une période sans activité : la session reste ouverte, le mot de passe est redemandé.",
                'reglages' => [
                    'verrouillage_actif' => ['libelle' => "Verrouiller l'écran après une période d'inactivité", 'aide' => 'Désactivé : aucun verrouillage automatique (le bouton « Verrouiller » du menu reste disponible).', 'type' => 'bool', 'defaut' => true],
                    'inactivite_minutes' => ['libelle' => "Délai d'inactivité avant verrouillage", 'aide' => "Sans clic, frappe ni mouvement de souris pendant ce délai, l'écran est verrouillé.", 'type' => 'int', 'defaut' => 20, 'min' => 1, 'max' => 480, 'unite' => 'minutes'],
                    'avertissement_secondes' => ['libelle' => 'Avertissement avant verrouillage', 'aide' => "Un compte à rebours s'affiche pendant ce délai avant le verrouillage, avec un bouton « Rester connecté ». 0 : pas d'avertissement.", 'type' => 'int', 'defaut' => 60, 'min' => 0, 'max' => 300, 'unite' => 'secondes'],
                    'ecran_max_minutes' => ['libelle' => "Durée maximale d'un verrouillage", 'aide' => 'Au-delà, la session est fermée : une connexion complète (adresse et mot de passe) est exigée.', 'type' => 'int', 'defaut' => 120, 'min' => 5, 'max' => 1440, 'unite' => 'minutes'],
                    'essais_max' => ['libelle' => 'Essais de déverrouillage autorisés', 'aide' => 'Après ce nombre de mots de passe incorrects, la session est fermée.', 'type' => 'int', 'defaut' => 5, 'min' => 1, 'max' => 20],
                ],
            ],
            'journal' => [
                'libelle' => "Journal d'activité",
                'description' => "Le journal lui-même n'est ni modifiable ni supprimable.",
                'reglages' => [
                    'export_max_lignes' => ['libelle' => "Nombre maximal de lignes d'un export du journal", 'aide' => '', 'type' => 'int', 'defaut' => 5000, 'min' => 100, 'max' => 50000],
                ],
            ],
        ];
    }

    public static function definition(string $cle): ?array
    {
        [$module, $reglage] = array_pad(explode('.', $cle, 2), 2, null);

        return self::catalogue()[$module]['reglages'][$reglage] ?? null;
    }

    /** Valeur courante : valeur modifiée si elle existe, sinon valeur par défaut. */
    public static function get(string $cle): mixed
    {
        $definition = self::definition($cle) ?? throw new InvalidArgumentException("Réglage inconnu : {$cle}");
        $surcharges = self::surcharges();

        if (! array_key_exists($cle, $surcharges)) {
            return $definition['defaut'];
        }

        [$valeur, $erreur] = self::normaliser($definition, $surcharges[$cle]);

        return $erreur ? $definition['defaut'] : $valeur;
    }

    /** Réglage verrouillé (ex. format de numérotation après le premier numéro émis) : la valeur ne peut plus changer. */
    public static function verrouille(string $cle): bool
    {
        $definition = self::definition($cle);
        if ($definition === null || ! isset($definition['verrou'])) {
            return false;
        }

        try {
            return (bool) ($definition['verrou'])();
        } catch (Throwable) {
            return false; // table pas encore migrée
        }
    }

    public static function estParDefaut(string $cle): bool
    {
        return ! array_key_exists($cle, self::surcharges());
    }

    /** @return array{0: mixed, 1: ?string} [valeur normalisée, message d'erreur] */
    public static function normaliser(array $d, mixed $brut): array
    {
        switch ($d['type']) {
            case 'bool':
                return [filter_var($brut, FILTER_VALIDATE_BOOLEAN), null];

            case 'int':
                if (! is_numeric($brut) || (int) $brut != $brut) {
                    return [null, 'nombre entier attendu.'];
                }
                $v = (int) $brut;
                if ($v < ($d['min'] ?? PHP_INT_MIN) || $v > ($d['max'] ?? PHP_INT_MAX)) {
                    return [null, "valeur comprise entre {$d['min']} et {$d['max']} attendue."];
                }

                return [$v, null];

            case 'texte':
                $v = trim((string) $brut);
                if ($v === '' || mb_strlen($v) > ($d['max'] ?? 255)) {
                    return [null, 'texte obligatoire de '.($d['max'] ?? 255).' caractères maximum.'];
                }
                if (isset($d['valider']) && ($erreur = ($d['valider'])($v))) {
                    return [null, $erreur];
                }

                return [$v, null];

            case 'choix':
                $valides = array_map('strval', array_keys($d['options']));

                return in_array((string) $brut, $valides, true) ? [(string) $brut, null] : [null, 'choix invalide.'];

            case 'liste':
                $demandes = array_map('strval', array_merge($d['obligatoires'] ?? [], (array) $brut));
                $liste = array_values(array_filter(
                    array_map('strval', array_keys($d['options'])),
                    fn ($option) => in_array($option, $demandes, true)
                ));

                return $liste ? [$liste, null] : [null, 'sélectionnez au moins un élément.'];
        }

        return [null, 'type de réglage inconnu.'];
    }

    /** Enregistre une valeur déjà normalisée. Retourne true si elle a changé. */
    public static function enregistrer(string $cle, mixed $valeur): bool
    {
        $definition = self::definition($cle) ?? throw new InvalidArgumentException("Réglage inconnu : {$cle}");
        $avant = self::get($cle);

        if (json_encode($avant) === json_encode($valeur)) {
            return false;
        }
        if (self::verrouille($cle)) {
            throw new DomainException($definition['verrou_message'] ?? 'Ce réglage est verrouillé.');
        }

        if (json_encode($valeur) === json_encode($definition['defaut'])) {
            Reglage::where('cle', $cle)->delete(); // retour à la valeur par défaut : plus de ligne
        } else {
            Reglage::updateOrCreate(['cle' => $cle], ['valeur' => json_encode($valeur), 'modifie_par' => auth()->id()]);
        }
        self::oublier();

        Journal::noter('reglages', 'modification', "Réglage « {$definition['libelle']} » : ".self::afficher($definition, $avant).' → '.self::afficher($definition, $valeur),
            null, null, ['cle' => $cle, 'avant' => $avant, 'apres' => $valeur]);

        return true;
    }

    public static function retablir(string $cle): void
    {
        $definition = self::definition($cle) ?? throw new InvalidArgumentException("Réglage inconnu : {$cle}");
        $avant = self::get($cle);
        if (self::verrouille($cle)) {
            throw new DomainException($definition['verrou_message'] ?? 'Ce réglage est verrouillé.');
        }

        Reglage::where('cle', $cle)->delete();
        self::oublier();

        Journal::noter('reglages', 'retablissement', "Réglage « {$definition['libelle']} » rétabli à sa valeur par défaut : ".self::afficher($definition, $avant).' → '.self::afficher($definition, $definition['defaut']),
            null, null, ['cle' => $cle, 'avant' => $avant, 'apres' => $definition['defaut']]);
    }

    /** Texte lisible d'une valeur (écran et journal). */
    public static function afficher(array $d, mixed $valeur): string
    {
        return match ($d['type']) {
            'bool' => $valeur ? 'Oui' : 'Non',
            'choix' => (string) ($d['options'][$valeur] ?? $valeur),
            'liste' => implode(', ', array_map(fn ($v) => $d['options'][$v] ?? $v, (array) $valeur)) ?: '—',
            default => (string) $valeur.(isset($d['unite']) ? ' '.$d['unite'] : ''),
        };
    }

    public static function oublier(): void
    {
        self::$surcharges = null;
    }

    private static function surcharges(): array
    {
        if (self::$surcharges === null) {
            try {
                self::$surcharges = Reglage::query()->pluck('valeur', 'cle')->map(fn ($v) => json_decode($v, true))->all();
            } catch (Throwable) {
                self::$surcharges = []; // table pas encore migrée : valeurs par défaut
            }
        }

        return self::$surcharges;
    }
}
