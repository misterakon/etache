# e-Tâche : état d'avancement et reste à faire
Document de reprise pour un nouveau chat. Dernière mise à jour : 10 octobre 2026.

## 0. Pour reprendre
1. Charger dans le projet (ou joindre au nouveau chat) : ce fichier, `F9_modeles_papier.md`, `A_reprendre_apres_F9.md`, `A_reprendre_organigramme_personnel.md`, `F9_etape1_modifications.md`, `F9_etape2_modifications.md`, `F10_organigramme_etape1_modifications.md`, `F10_organigramme_etape2_modifications.md`.
2. Joindre aussi, si possible, les fichiers **réellement en production** que je dois modifier (mes copies viennent de la version du projet et peuvent être périmées) : `Personnel.php`, `MandatResponsable.php`, `DgepnController.php`, `UtilisateurController.php`, `ResponsableController.php`, `routes/web.php`, `sidebar.blade.php`, `AppServiceProvider.php`, et `dgepn/js_script.blade.php` (non vu à ce jour).
3. Préférences de l'utilisateur : réponses **concises en français**, ne pas réécrire un fichier entier quand quelques lignes changent, se concentrer sur le code utile. Livrer avec un tableau « Où déposer », les tests à faire, et dire clairement ce qui n'a pas pu être testé.

## 1. Le projet
Application Laravel 13 / PHP 8.3 de suivi de la feuille de route annuelle de la **DGEPN** : exercices, axes, actions clés, actions mesurables, tâches, sous-tâches, plus un module **Courrier** et la gestion du **personnel / organigramme**.
Profils : ADMIN, DG, RD, RA, et **COURRIER** (service courrier et secrétariat du DG).
Convention de code : formulaires en modal `form_xxx.blade.php` + script `js_xxx.blade.php`, envoi classique (POST + redirection), suppression en AJAX.

### Pièges techniques déjà rencontrés (à respecter)
- **jQuery n'est pas chargé** quand les scripts en ligne s'exécutent : lier les événements en JS natif ; n'utiliser `$` que dans des fonctions appelées plus tard.
- **`$.fn.modal` est redéfini** dans `layout/script.blade.php` : ouvrir avec `$('#modal').modal('show')`, jamais l'API Bootstrap native. Déplacer le modal sous `<body>` avant de l'ouvrir.
- Le thème impose un `display` aux `<span>` : utiliser `display: ... !important` pour les éléments en ligne stylés.
- La directive Blade **`@json(...)` découpe son argument sur les virgules** : calculer le tableau dans un `@php` puis `@json($variable)`.
- La classe de grille **`card-gap` n'espace que les colonnes** : poser explicitement un `gap` pour l'espacement vertical.
- Grille du thème : `grid grid-cols-12`, `col-span-N`, `md:col-span-12`, `xl:col-span-12`.
- Dans le sandbox de l'assistant, PHP a été installé pour contrôler la syntaxe : refaire `apt-get update && apt-get install -y php-cli` si besoin. Des contrôleurs de vues Blade/JS maison ont servi (non conservés).

## 2. Ce qui est livré
### Chat précédent (F2 à F7, Réglages) : voir le résumé du début de ce projet
F2 exports, F3 Gantt, F4 sous-tâches et avancement calculé, F5 fichiers livrables, F6 journal d'activité, F7 clôture, verrouillage et reconduction d'exercice, écran Réglages. Document Word des règles de gestion livré (`e-Tache_Regles_de_gestion.docx`).

### F9 Courrier, étape 1 : registre des arrivées (migrations 36 à 40)
Registre avec indicateurs et filtres, enregistrement dans une grande fenêtre, **scan en glisser-déposer avec aperçu**, **visionneuse dans la page**, détection de doublon, courrier confidentiel (visibilité restreinte et consultations tracées), types de courrier et urgences, paramétrage ADMIN (blocs pliables), module Réglages « Courrier ». Numérotation **A26-0001** (arrivée) et **D26-0001** (départ), format verrouillé après le premier numéro. Champs de la fiche : mode de transmission, pièces jointes. La mention rouge « B » du registre papier est **abandonnée**.

### F9 Courrier, étape 2 : imputation et diligences (migrations 41, 42)
Le **DG impute** (aussi l'ADMIN) à des unités, des postes (DGA, chargé d'études, service courrier) ou **directement à un agent** : un responsable principal, des associés pour avis ou pour copie ; 12 instructions du DG ; délais Urgent, 24 h, 48 h, Autre. Une **diligence par destinataire** (`DL26-0001`), notification (cloche), **supérieur informé** en cas d'imputation directe à un agent (réglable), **prise en charge**, **solder**, **annuler**. Écrans : tableau des diligences, « Mes courriers à traiter », page d'une diligence. **Fiche de transmission en PDF** (modèle papier). Page de détail d'un courrier réorganisée (fiche 4 + visionneuse 8, puis imputation 8 + parcours 4).

### Chantier organigramme / personnel (migrations 43 à 45)
- **Tranche 1** : table des **fonctions** (DG, DGA, directeur, sous-directeur, chef de service, chargé d'études, conseiller, agent, technicien de surface…), **affectation à un poste dans une fenêtre** (liste du personnel et fiche), **intérim**, confirmation de remplacement, **fin d'affectation** (la date saisie est le **dernier jour au poste, inclus** : l'agent reste en poste jusqu'à minuit), le champ `fonction` contient la valeur du poste (« Directeur - Direction X », « Agent »…), un agent n'est titulaire que d'un poste, un titulaire remplacé perd sa fonction, **page Responsables en consultation seule**, colonne **Compte** dans la liste, création de compte limitée aux agents sans compte avec nom et email pré-remplis.
- **Tranche 2** : listes (genre, civilité, statut matrimonial, types de documents), **échelle de traitement** (12 grades, 4 classes, échelons 1 à 4, 153 indices, sans écran), nouveaux champs de l'agent (civilité, genre, statut matrimonial, enfants, père, mère, tél 2, grade, classe, échelon, dates de service, retraite, décès sans effet automatique), **dossier de l'agent** (consultation ADMIN, DG, agent ; dépôt ADMIN ; consultations tracées), **nouvelle fiche agent** (colonnes compactes, bandeau intact), formulaires agent sans fonction ni service, script `js_champs_agent` pour pré-remplir les nouveaux champs.

## 3. À vérifier dans l'application de l'utilisateur (modifications manuelles demandées)
- `AppServiceProvider` : alias polymorphes `courrier`, `imputation`, `diligence`, `personnel`.
- `routes/web.php` : routes courrier (étapes 1 et 2), `dgepn` (affectation, fin d'affectation, fonctions, carrière, dossier), menu « Courrier » et « Fonctions ».
- `Personnel` : champs fillable et casts, relations (`civilite`, `genre`, `statutMatrimonial`, `grade`, `classeAgent`, `echelon`, `fonctionDef`), `postesActuels()` avec `id` et `debut_iso`.
- `MandatResponsable` : constante `NIVEAU_DGA`, méthode `libellePoste()` (séparateur « - »).
- `DgepnController::personnels()` (chargement de `user` et des mandats) et **`storePersonnel()`** : retirer `service_id` et `fonction`, ajouter les nouveaux champs. **Sans cela, les nouveaux champs du formulaire agent ne sont pas enregistrés.**
- `UtilisateurController::comptes()` (agents avec `user` et `service`).
- `ResponsableController` : `abort(403)` en tête de `store()`, `fin()` et `destroy()`.
- Migrations 36 à 45 lancées dans l'ordre ; la migration 43 change le type de `mandats_responsables.niveau` (si elle échoue : `SHOW CREATE TABLE mandats_responsables;`).
- Les agents existants gardent leur ancien texte dans `fonction` jusqu'à leur première affectation. Une commande de reprise depuis les mandats actuels a été **proposée, pas validée**.

## 4. Reste à faire, par ordre proposé
### 4.1 Courrier, étape 3 (réimputation, traitement, réponse)
- **Réimputation en cascade** : chaque destinataire réimpute vers le bas (direction, sous-direction, service, agent) avec historique et renvoi motivé ; le **DGA réimpute**, le **chargé d'études reçoit seulement** (colonne « peut réimputer » des postes).
- **Traitement** d'une diligence : sous-tâches, fichiers, demande de délai (circuit de report existant).
- **Réponse** : projet joint, visas, signature scannée déposée et tracée ; plusieurs réponses possibles.
- **Pré-tri** par le cabinet : optionnel, désactivé par défaut.
- Questions à poser : la réponse monte-t-elle toute la chaîne hiérarchique pour visa, ou le DG seul vise et signe-t-il ? Une réponse signée crée-t-elle automatiquement le courrier départ ? Une réimputation peut-elle viser plusieurs destinataires comme celle du DG ?

### 4.2 Courrier, étape 4 (départ, archivage, alertes, exports)
- **Registre départ** `D26-0001` : **un numéro par destinataire** (comme sur le registre papier : 110, 111…), objet saisi une fois, lien avec le courrier d'origine, nombre de pièces, **n° d'archives saisi à la main** (référence de rangement).
- **Archivage** consultable, champ « à conserver jusqu'au », aucune suppression automatique.
- **Alertes d'échéance et de retard** sur les diligences (aujourd'hui « en retard » ne s'affiche qu'à l'écran).
- **Exports** : registre départ, diligences par statut, direction et période (demande du DG). Le registre arrivée s'exporte déjà.
- Graphiques (F12) et compteurs agrégés pour l'IA (F8), jamais l'objet des courriers.

### 4.3 Courrier et organigramme
- **Postes destinataires** (Courrier > Paramétrage) : se limiter à **sélectionner les postes de l'organigramme** et indiquer qui peut réimputer ; les destinataires sont liés au personnel et à ces postes.
- **Règle proposée, à valider** : une diligence imputée à un **poste suit le poste** (le nouveau titulaire, ou l'intérimaire pendant l'intérim) ; imputée à un **agent**, elle reste à l'agent ; **réimputée** plus bas, elle reste chez la personne désignée ; **soldée ou annulée**, elle est figée. Notifier l'ancien et le nouveau titulaire, tracer dans le journal, en faire un réglage.

### 4.4 Instruction du Directeur Général : décomposition des tâches en sous-tâches
> Chaque responsable décompose une tâche en sous-tâches **au bénéfice de l'agent concerné**, précise pour chacune les **modalités et conditions d'exécution**, et toute sous-tâche définie est **validée par le supérieur hiérarchique compétent** (conformité et traçabilité).
Existe déjà (F4) : sous-tâches cochables, avancement calculé, fichiers, journal. Manque : agent destinataire par sous-tâche, modalités et conditions, circuit à valider / validée / refusée avec motif, traçabilité. Détails et questions dans `A_reprendre_apres_F9.md` (qui est le supérieur compétent ? les sous-tâches non validées comptent-elles dans l'avancement ? etc.).

### 4.5 Autres chantiers déjà identifiés
0.6 instantané quotidien, F8 synthèses, F11 RH, F12 graphiques Chart.js, F13 sécurité (dont le **contrôle d'accès côté serveur** des écrans Paramètres, Utilisateurs et Nominations, aujourd'hui limité à l'affichage du menu), F1 **annulation de tâche** (mise de côté).
Points ouverts de l'ancien chat : import de matrice qui refuse un axe de même libellé dans un autre exercice, suppression d'une tâche reconduite qui ne lève pas le marquage de la source, absence d'unicité de l'exercice actif, `SHOW CREATE TABLE axes` jamais fourni (la migration 34 suppose l'index `axes_libelle_unique`).
**Recette non faite** : F6 (journal), F7 (clôture, reconduction), Réglages ; et toutes les tranches de cette session sont livrées **sans test en conditions réelles** (seule la syntaxe a été contrôlée).

## 5. Décisions déjà prises (ne pas redemander)
- Numérotation : `A26-0001`, `D26-0001` ; diligences `DL26-0001`.
- Le DG impute, puis escalade d'imputation vers le bas ; il peut aussi imputer **directement à un agent**, auquel cas le supérieur est informé (réglable).
- Les diligences ont leurs **propres écrans**, hors Gantt, suivi de la feuille de route et clôture d'exercice.
- Le **système de postes ne remplace pas les mandats** : même principe, délocalisé dans la fenêtre d'affectation (liste du personnel et fiche). Un seul titulaire par poste de direction ; intérim possible ; un nouveau titulaire remplace l'ancien après confirmation.
- Grade, classe, échelon : **en base uniquement**, aucun écran. Autres listes proposées par défaut, valeurs à confirmer.
- Dossier de l'agent : consultable par ADMIN, DG et l'agent ; dépôt et suppression par l'ADMIN. Dates de décès et de retraite : **aucun effet automatique**.
- Chargés d'études et conseillers : rattachés à la Direction Générale.
- Fiche agent : ne pas toucher à l'arrière-plan du bandeau.

## 6. Questions encore ouvertes pour l'utilisateur
1. Valeurs définitives des listes genre, civilité, statut matrimonial.
2. Le « Sous-directeur - … » de la fonction doit-il porter le nom de la sous-direction (comportement actuel) ou celui de la direction ?
3. Valider la règle de suivi des diligences lors d'un changement de titulaire (4.3).
4. Réponses aux questions de l'étape 3 du courrier (4.1).
5. Souhaite-t-il une commande qui reprend les mandats actuels pour renseigner d'un coup `fonction` et `fonction_id` des agents ?
