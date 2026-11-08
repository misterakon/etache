# e-Tache — Suite du projet (reprise pour un nouveau tchat)

> À lire avec `reprise_projet_etache.md` (socle, modèle de données, conventions). Ce fichier couvre ce qui a été fait ensuite, les décisions prises et le plan par tranches.
> Dernière mise à jour : 4 octobre 2026.

## 0. Consignes de travail
- Réponses en français, **concises**. Ne pas réécrire un fichier entier si quelques lignes changent (donner l'extrait exact). Fournir un fichier complet pour une vue Blade modifiée si l'utilisateur le demande ou se perd dans les modifications.
- Respecter la méthode de l'utilisateur : formulaires `form_*`, scripts dans `js_script.blade.php` (jQuery + Swal), un bloc par fonctionnalité, vues dans `resources/views/feuille_de_route/`.
- Claude ne peut pas exécuter Laravel : il vérifie la syntaxe (`php -l`, `node --check`, équilibre des balises). L'utilisateur teste et renvoie les erreurs.
- Au plus une question par réponse. Avant de coder : demander les fichiers utiles.
- Chaque page charge **sa propre feuille de style** (`tache-list.css`, `tache-details.css`, `mes-taches.css`) qui définit les classes `tl-*` (badges). Une nouvelle page doit charger `tache-list.css`, sinon les badges n'ont pas de fond.
- DataTables : `assets/js/datatable/datatables/dataTables1.js` + `datatable.custom2.js`, table `id="data-source-1"` (tri croissant par défaut sur la 1re colonne).
- Les routes du module portent le préfixe de nom `feuille_de_route.` ; les notifications sont `notifications.*`.

## 1. Ce qui est fait

### 1.1 Justification de retard avec approbation (terminé et validé)
Règles : demande par le responsable ou le pilote de la tâche (ou l'admin) ; une seule demande en attente par tâche ; deux types (`justification`, `report`) ; report ≤ 15 jours et premier report → RD, sinon DG ; l'admin décide tout ; personne ne décide sa propre demande (admin compris) ; refus toujours motivé ; acceptation = nouvelle échéance, journalisée avec le décideur comme auteur.

Fichiers : `database/migrations/2026_10_02_100000_create_tache_justifications_table.php`, `Models/TacheJustification.php` (scope `aDecider`), `Support/JustificationRetard.php` (`decideurPour`, `contexte`, `nbAValider`, `retardsNonJustifies`), `Http/Controllers/JustificationController.php` (`index`, `store`, `decision`, `annuler`), vues `justification-a-valider`, `_modal_justification`, `_btn_justifier`, `_badge_justification`, `tache-details`, `mes_taches`, bloc « JUSTIFICATION DE RETARD » dans `js_script`. Relation `Tache::justifications()`. Routes : `tache.justification.store`, `justification.decision`, `justification.annuler`, `justification.index`. Menu : « Demandes à valider » avec compteur (ADMIN|DG|RD). Droits côté contrôleur : `exigerProfils('ADMIN|DG|RD')` sur `storeTache`, `getTache`, `deleteTache`.

Reste de ce chantier : indicateur « retards non justifiés » au tableau de bord (requête prête : `JustificationRetard::retardsNonJustifies()`), question ouverte « un report fait directement par « Modifier » doit-il exiger un motif ? », notifications des événements de justification dans la cloche.

### 1.2 Notifications (terminé et testé)
- **Moteur** : `Support/MoteurAlertes.php` + commande `php artisan alertes:evaluer [--simuler] [--date=AAAA-MM-JJ] [-v]`. Déclenchement le jour exact `échéance + décalage` (pas de rattrapage). Anti-doublon par (tâche, règle, canal, agent, échéance) via `alertes_envoyees.echeance_au_envoi` + index unique `alertes_envoyees_anti_doublon`. Tâches suspendues ignorées ; une demande de justification en attente (< 7 jours) met les relances en pause. Seuls les canaux actifs (application 001, email 002) sont utilisés ; SMS ignoré. Agent sans email ou sans compte actif : ignoré et signalé. Échecs : console et log seulement.
- **Situations temporelles** mises à jour par le même passage : terminée, en retard, à risque (bloquée, dépendance en retard, ou avancement en retard d'au moins 30 points sur le temps écoulé), échéance proche (≤ 7 jours), normal. Constantes en haut de `MoteurAlertes`. Seuils à valider avec les directions.
- **Notification** : `Notifications/AlerteTacheNotification.php` (canaux `database` et `mail`, locale `fr`). Gabarit email : `resources/views/emails/alerte_tache.blade.php`. Traductions : `lang/fr.json`.
- **Cloche** : `NotificationController` (`index`, `ouvrir`, `toutLire`), `notifications/_cloche.blade.php` (non lues uniquement, compteur réel), `notifications/index.blade.php` (« Mes notifications », 300 dernières, filtres Toutes / Non lues), `layout/header.blade.php` (lien Profil masqué si le compte n'a pas d'agent lié). Routes : `notifications.index`, `notifications.ouvrir`, `notifications.tout_lire`.
- **Prérequis rencontrés** : `Relation::enforceMorphMap` doit contenir `'user' => User::class` ; migration `notifications` protégée par `Schema::hasTable` ; `MAIL_ENCRYPTION=tls` (et non `tsl`) ; Gmail exige un mot de passe d'application ; `APP_NAME="e-Tache"`, `APP_LOCALE=fr`.
- **Planificateur** : `routes/console.php` → `Schedule::command('alertes:evaluer')->dailyAt('07:30')->timezone('Africa/Abidjan')->withoutOverlapping();`. Lancement local : `php artisan schedule:work` (terminal ouvert, mise en veille désactivée). À vérifier : l'heure est bien revenue à 07:30 après les tests.
- **Journal des alertes** : réalisé (page existante), à enrichir plus tard avec les échecs.

### 1.3 À vérifier après les tests
- Remise en état de la tâche TA26-010 (échéance d'origine `2026-10-06`), suppression des alertes et notifications de test.
- Le premier vrai passage a mis à jour la situation temporelle de toutes les tâches (43 modifiées lors du test).

## 2. Décisions de gestion

### 2.1 Profils et droits
- **DG** : fonctionne par son profil, sans mandat requis ; voit tout.
- **Administrateur** : crée les comptes, donne le profil RD à la main, nomme les responsables ; avec le DG, seul à créer, modifier, supprimer les tâches.
- **RD** : consulte **toutes** les tâches en **lecture seule** ; décide les reports de sa direction. Ses pouvoirs exigent **le profil RD ET un mandat de responsable de direction en cours** ; sans mandat, il se comporte comme un RA (voit et met à jour ses tâches) avec le message « Aucune direction ne vous est rattachée, contactez l'administrateur ». Un RD peut diriger plusieurs directions (cumul, intérim).
- **Suivi de l'exécution du RD** : directions dont il est responsable (principales ou associées) **plus** les tâches « transversales » (un agent de sa direction est responsable ou pilote). Les simples contributeurs n'élargissent pas le périmètre. Le DG et l'admin voient tout.
- **RA** : met à jour l'avancement de ses tâches, demande justification ou report.
- Garde-fous : badge « RD sans mandat » dans la liste des utilisateurs, avertissement à la création d'un compte RD sans mandat (sans bloquer).
- Direction sans responsable : les demandes de report montent au DG ; la direction est suivie par le DG et l'admin ; la règle d'alerte « responsable de direction » n'a alors pas de destinataire (signalé en console).
- Un seul endroit doit répondre à « de quelles directions cet utilisateur est-il responsable aujourd'hui ? » (via les mandats), réutilisé par menus, listes, suivi et droits.

### 2.2 Directions d'une tâche
- Une ou **plusieurs directions par action clé** : une principale et des associées. Les actions mesurables et les tâches en héritent (rien à saisir au niveau action mesurable ni tâche).
- **Direction porteuse** = celle de l'action clé (stable, sert aux rapports, à la clôture, à la reconduction). **Directions impliquées** = déduites des affectations (responsable ou pilote), pour la visibilité.
- Éviter le double comptage : une action partagée compte une seule fois dans les totaux (via la direction principale). L'archive de clôture fige les directions à la date de clôture.
- Bug existant : `Tache::directions()` ne suit que le chemin par la sous-direction et oublie les services rattachés directement à une direction.
- Le filtre `Tache::scopeParDirection` mélange porteuse et impliquée (contributeurs compris) : à corriger. La liste des tâches doit permettre de chercher par direction avec le choix porteuse / impliquée / toutes, et afficher les badges « Porteuse » / « Transversale ».

### 2.3 Cascade de sélection
- Hiérarchie : Exercice → Axe → Action clé → Action mesurable → Tâche. L'exercice se déduit de l'axe (pas stocké sur les enfants).
- Constat : le formulaire de tâche n'a qu'un menu « Action mesurable » listant toutes celles de tous les exercices (idem pour les actions clés dans le formulaire d'action mesurable) : ingérable dès la 2e année.
- Cible : sur tous les formulaires (axe, action clé, action mesurable, tâche) et toutes les listes, une cascade Exercice → Axe → Action clé → Action mesurable ; seul le dernier niveau est enregistré ; exercice par défaut (`Exercice::parDefaut()`) présélectionné ; niveaux grisés tant que le parent n'est pas choisi ; recherche (Select2) sur le dernier niveau ; modification = chaîne préremplie ; exercice clos = lecture seule. Un seul bloc partagé (partiel de formulaire + fonction dans `js_script`), structure de la matrice d'un exercice chargée une fois en JSON.

### 2.4 Matrice annuelle et clôture
- La matrice est chargée **par fichier Excel** ou saisie par formulaire. Aucune fonction d'import n'est visible dans le projet : demander comment le chargement se fait aujourd'hui et un exemple de fichier.
- **Clôture d'exercice** : les tâches non terminées sont **figées en lecture seule** et restent visibles. Contrôle préalable, verrouillage côté serveur (création, modification, suppression), date et auteur de clôture, archive Excel, réouverture possible par l'admin.
- **Reconduction** : décidée par le **DG** (et l'admin), après chargement de la nouvelle matrice. Le DG choisit les tâches non terminées à reconduire, la **action mesurable de destination** (suggestion par libellé identique) et une **nouvelle échéance obligatoire**. Une nouvelle tâche est créée (nouveau code TA27-…) avec libellé, indicateur, livrable, priorité, équipe, avancement et statut repris ; dépendances non reprises ; la tâche d'origine reste figée avec lien « Reconduite » ; une tâche ne peut pas être reconduite deux fois ; opération journalisée.
- Séquence annuelle : clôture → import de la nouvelle matrice → reconduction.

### 2.5 Barre de progression pour l'analyse et l'import (idée validée à intégrer à la tranche 5)
- Affichage en direct : étape en cours (lecture du fichier, vérification des directions, des agents, création des axes, actions clés, actions mesurables, tâches), compteur « lignes traitées / total », pourcentage, erreurs détectées au fil de l'eau, bouton Annuler pendant l'analyse, récapitulatif final. Réutilisable pour les exports volumineux et l'archive de clôture.
- Piste recommandée : un job en arrière-plan (file `database`, second terminal `php artisan queue:work`, comme `schedule:work`) ; le navigateur interroge une route de progression toutes les secondes. Une table `imports` garde statut, étape, total, traitées, erreurs.
- Contrainte : l'import reste « tout ou rien » dans **une seule transaction** ; la progression doit donc être écrite **hors transaction** (cache), sinon elle reste invisible jusqu'à la validation.
- Alternative sans worker : traitement par lots pilotés par le navigateur pour l'analyse (lecture seule), puis import en une requête avec progression lue dans le cache. Attention : `php artisan serve` est mono-requête et bloquerait la lecture de la progression ; Apache de WAMP convient.

## 3. Plan par tranches

| # | Tranche | Contenu | Fichiers à fournir |
|---|---|---|---|
| 0 | **Correctif des responsables** | Un seul responsable par unité à une date donnée (départage `scopePrioritaire` : ajouter `->orderByDesc('id')`) ; « Mettre fin » seulement sur le responsable en poste ; ancien marqué « Remplacé » | `ADesResponsables.php`, `ResponsableController.php`, vue Responsables + JS |
| 1 | **Directions multiples sur l'action clé** | Principale + associées, reprise des données existantes, héritage, formulaire d'action clé, correction de `Tache::directions()` | `ActionCle.php`, `Axe.php`, `Exercice.php`, `SHOW CREATE TABLE` des actions clés, vue Actions clés + formulaire |
| 2 | **Droits et périmètre du RD** | Méthode unique « directions du responsable aujourd'hui », lecture seule du RD, périmètre du Suivi, retrait de `RD` de `exigerProfils` pour créer/modifier/supprimer, RD sans mandat = RA, badge « RD sans mandat », menus et boutons alignés | `ListeTachesDonnees.php`, `MainController.php`, classe du Suivi de l'exécution, `ADesProfils` |
| 3 | **Cascade sur les formulaires** | Bloc partagé, 4 formulaires, recherche, préremplissage, lecture seule si clos | Vues `axe`, `action_cle`, `action_mesurable` + formulaires (déjà : `form_tache`, `js_script`) |
| 4 | **Listes en cascade + recherche par direction** | Barre de filtres identique, filtre Direction (porteuse / impliquée / toutes), badges | — |
| 5 | **Import Excel de la matrice** | Modèle téléchargeable (colonnes dont direction principale et directions associées), prévisualisation, validation ligne par ligne, rattachement par codes (directions) et matricule ou email (agents), tout ou rien, sans doublons, refusé si exercice clos, rapport ; **barre de progression dynamique** à l'analyse et à l'import (voir 2.5) | Exemple du fichier Excel, `composer.json` |
| 6 | **Exports Excel et PDF** | Une page « Exports » : liste des tâches, synthèse par direction ou axe, rapport mensuel ; paquets prévus : `maatwebsite/excel`, `barryvdh/laravel-dompdf` | Modèle du rapport mensuel, `DashboardDonnees` |
| 7 | **Clôture d'exercice** | Voir 2.4 | `SHOW CREATE TABLE exercices`, `ParametreController.php`, vue Exercices |
| 8 | **Reconduction par le DG** | Voir 2.4 ; prérequis : tranches 5 et 7 | — |

**À intercaler** : indicateur « retards non justifiés » du tableau de bord (`MainController`, `DashboardDonnees`, vue du dashboard) ; notifications des justifications dans la cloche (demande reçue, décision rendue) ; middlewares sur les routes ; planificateur qui redémarre seul + contrôle de santé ; tests automatisés (décideur selon le report, anti-doublon, situations temporelles, droits) ; passage en production (`APP_DEBUG=false`, HTTPS, caches, emails en file d'attente).

**Mis de côté** : sauvegardes (page admin, dump MySQL quotidien compressé avec rotation, copie hors machine, test de restauration dans une base temporaire). Besoins le moment venu : section `DB_` du `.env` sans mot de passe, `dir C:\wamp64\bin\mysql`.

**Ordre conseillé** : 0, puis 3 (gêne déjà ressentie sur les formulaires), puis 1, 2, 4, 5, 6, 7, 8.

## 4. Points ouverts
- Qui peut reconduire : DG et admin retenus ; confirmer si chaque RD doit pouvoir reconduire les tâches de sa direction.
- Comment la matrice est chargée aujourd'hui, et structure exacte du fichier Excel (une feuille ou plusieurs).
- Seuils de la situation « à risque » et règles d'alerte (J-30 à J+30 ; J+30 est désactivée) à valider avec les directions.
- Seuil de 15 jours pour le report décidé par le RD à confirmer.
- Un responsable d'action mesurable d'une autre direction rend-il cette direction « impliquée » pour les tâches en dessous ? Proposition : non.
- Report fait directement par « Modifier » : motif obligatoire ou non.
- Le menu montre encore « Axes », « Actions clés », « Actions mesurables » aux RA (prévu ADMIN et DG).
- La page Alertes et le Suivi de l'exécution sont cachés au RD dans le menu alors que les routes les prévoient (`ADMIN,DG,RD`) : à aligner avec la tranche 2.

## 5. Présentation
Un déroulé de 8 diapositives (contexte, e-Tache en une image, piloter avec démo, organisation et droits, alertes, justification, évolution, vision RH et patrimoine) et le texte complet à dire ont été préparés dans le tchat précédent. Le PowerPoint n'a pas encore été généré.
