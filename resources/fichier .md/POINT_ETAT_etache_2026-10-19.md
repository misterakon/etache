# e-Tâche DGEPN : point d'état, fonctionnalités et règles de gestion

Date : 19 octobre 2026
Sources : fiche de reprise du 15/10/2026, `contexte_projet.md`, `Reste_a_faire_etache`, `Feuille_de_route_F9_a_F13`, documents F9/F10, et le travail de cette session.
Fiabilité : les éléments marqués *(fiche 15/10)* viennent des documents de reprise et n'ont pas été relus dans le code pendant cette session. Rien n'a été exécuté dans l'application réelle : seules la syntaxe et des tests simulés ont été contrôlés.

---

## 1. Contexte technique

- Application Laravel 13, PHP 8.3, thème CUBA (Tailwind), **menu horizontal imposé**. Locale `fr`.
- Local : WAMP64 (`C:\wamp64\www\etache`, `127.0.0.1/etache`). Production : hébergement en ligne payé, contrôle serveur limité.
- Authentification Breeze (inscription publique et réinitialisation par email désactivées : usage interne).
- Modales : shim jQuery `.fn.modal` à la place de Bootstrap JS.
- Planificateur : `schedule:run` chaque minute (en local `schedule:work`).

---

## 2. État d'avancement par module

| Module | État |
|---|---|
| Paramètres (exercices, types agent, familles d'emploi, emplois, profils, rôles d'affectation, priorités, statuts, situations temporelles, canaux, règles d'alerte) | Fait |
| DGEPN : directions, sous-directions, services, personnel (photo, fiche) | Fait |
| Utilisateurs (comptes liés à un agent, profil d'accès) | Fait |
| Feuille de route : axes, actions clés, actions mesurables, tâches, dépendances | Fait |
| Vue d'ensemble (lecture seule de toute la hiérarchie) | Fait |
| Charge de travail (vue Direction Générale) | Fait, en affinage (tableaux et pagination à valider en navigateur) |
| Import de la matrice Excel | Fait (`import:matrice`) |
| Sous-tâches (F4b), fichiers, avancement calculé, journal | Fait *(fiche 15/10)* |
| Validation de la clôture d'une tâche par le supérieur | Fait, réglage désactivé par défaut *(fiche 15/10)* |
| Courrier arrivée / départ / diligences (F9, étapes 1 à 4) | Fait *(fiche 15/10)* |
| Pilotage et analyses (F8 + F12), synthèse par modèles ou IA, exports | Fait *(fiche 15/10)* |
| Personnel, organigramme, dossier, carrière (F10) | Largement fait *(fiche 15/10)* |
| Sécurité des routes (F13 partie 1), page 403, menu horizontal | Fait *(fiche 15/10)* |
| **Sous-menu Tâches : Liste** | Fait (modèle de référence `tache.blade.php`) |
| **Sous-menu Tâches : Mes tâches** | **Livré cette session, à tester** |
| **Détails d'une tâche** | **Livré cette session, à tester** |
| Sous-menu Tâches : Matrice, Kanban, Calendrier, Gantt, Cartes, Arborescence, Timeline | À faire (gabarits fournis : `kanban`, `tache_calendar`, `timeline`, `tache-list`) |
| Vue panoramique des tâches (modèle `taches-details_all`) | À faire |
| Moteur d'alertes (commande planifiée alimentant `alertes_envoyees`) | À vérifier : la fiche cite des alertes de diligences et RH, pas celles des tâches |
| Notifications email / SMS réelles | À faire |
| Congés et absences (F11) | En attente des règles de la DRH |
| Fin de F13 : sauvegardes, HTTPS, mise en production | À faire |
| Recette complète (`plan_de_recette_etache.xlsx`, 177 tests, 24 modules) | À faire par l'utilisateur |

---

## 3. Fonctionnalités par module

### 3.1 Feuille de route et tâches
- Hiérarchie : **Exercice > Axe > Action clé > Action mesurable > Tâche**, chaque niveau avec un `ordre` et un code généré automatiquement.
- Tâche : libellé, indicateur, livrable, dates, durée, priorité, budget estimé, statut d'exécution, situation temporelle, avancement en %, observations, dernière mise à jour.
- Affectations polymorphiques (action clé, action mesurable, tâche) avec trois rôles : **Responsable, Pilote, Contributeur**.
- Dépendances entre tâches (une tâche peut en bloquer d'autres).
- Filtres centralisés dans `Tache::filtrer()` : texte, axe, action clé, action mesurable, statut, priorité, périodes d'échéance, retard, direction, responsable, pilote, contributeur, « mes tâches ».
- Page **Liste** : tableau `basic-6`, avatars des responsables, boutons de 30 px.
- Page **Vue d'ensemble** : hiérarchie complète en lecture seule.
- Page **Charge de travail** : 8 onglets (Synthèse, Retards, Échéances proches J-30, En cours, Directions, Agents, Sans responsable, Activité), graphiques ApexCharts, filtres direction et exercice.

### 3.2 Mes tâches (livré cette session)
- Menu gauche faisant office d'onglets : Tâches du jour, Toutes mes tâches, En cours, En attente, Terminées, avec compteurs.
- **Tâches du jour** : Basic Timeline du template, un point coloré par tâche, barre d'avancement, lien vers les détails, bouton de mise à jour.
- **Toutes mes tâches** : tableau paginé au modèle de référence. Les autres onglets restent en liste simple.
- **Modal de mise à jour** : priorité, statut d'exécution, avancement.

### 3.3 Détails d'une tâche (livré cette session)
- En-tête : code, libellé, statut, priorité, retard, situation temporelle, avancement, début, échéance, durée, reste ou retard, budget, dernière mise à jour.
- Onglet **Aperçu** : rattachement (exercice, axe, action clé, action mesurable) avec le **nombre de tâches** par niveau, puis indicateur, livrable, observations.
- Onglet **Équipe et dépendances** : agents par rôle avec photo, service et lien vers la fiche ; directions concernées ; tâches dont elle dépend et tâches qu'elle bloque.
- Onglet **Historique et alertes** : modifications (date, champ, ancienne et nouvelle valeur, auteur) et alertes envoyées.

### 3.4 Courrier et diligences (F9) *(fiche 15/10)*
- Registre arrivée `A26-0001` : scan en glisser-déposer, visionneuse, détection de doublons, confidentialité, types et urgences paramétrables.
- Imputation par le DG avec 12 instructions paramétrables et délais ; diligences `DL26-0001`, prise en charge, solde, annulation, tableau des diligences, « Mes courriers à traiter », fiche de transmission PDF, notifications.
- Réimputation en cascade (parent et enfants, statut « Réimputée »), associés en avis ou copie avec dépôt d'avis.
- Réponse : projet joint, visas, signature scannée, plusieurs réponses possibles ; registre départ `D26-` (un numéro par destinataire) ; archivage ; exports.
- Carte « Où en est ce courrier ? » : détenteur, circuit de traitement, circuit de réponse.

### 3.5 Pilotage et analyses (F8 + F12) *(fiche 15/10)*
- Périodes année, trimestre, mois ; périmètre axe ou direction ; graphiques SVG générés côté serveur.
- Synthèse par modèles de phrases ou par IA (clé dans `.env`), génération dynamique, historique, exports Excel et note PDF.
- Instantané quotidien de l'avancement (première visite du jour et commande planifiée).
- Commande `etache:reinitialiser` pour repartir à neuf avant la recette.

### 3.6 Personnel et organisation (F10) *(fiche 15/10)*
- Fiche agent : identité modifiable, photo, informations administratives (civilité, genre, statut matrimonial, enfants, parents), carrière (grade, classe, échelon sur une échelle de 153 indices), dossier de documents.
- Postes, mandats et intérims, fonctions, page Responsables en consultation.
- Alertes RH planifiées.

---

## 4. Règles de gestion

### 4.1 Codes et numérotation
- Tâche : `TA` + année sur 2 chiffres + numéro sur 3 chiffres (`TA26-001`), l'année venant de l'exercice du rattachement.
- Courrier arrivée `A26-0001`, diligence `DL26-0001`, départ `D26-…`. Formats réglables dans les Réglages.

### 4.2 Statuts et paramètres (codes stables)
- Statuts d'exécution : 001 Non démarrée, 002 En cours, 003 Bloquée, 004 Suspendue, **005 Terminée**, 006 Annulée.
- Priorités : 001 Haute, 002 Moyenne, 003 Basse.
- Rôles d'affectation : 001 Responsable, 002 Pilote, 003 Contributeur.
- Profils d'accès (par **slug**, le `code` n'étant qu'un compteur technique) : `DG`, `RD` (responsable de direction), `RA` (responsable de tâche), `ADMIN`, `COURRIER`.

### 4.3 Retard
- Une tâche est **en retard** si son échéance est passée **et** qu'elle n'est ni terminée ni annulée (`estEnRetard()` / `enRetardCalcule`), indépendamment du moteur d'alertes.
- Avec la validation de clôture activée, le retard est calculé à la **date de soumission** et non à la date de validation *(fiche 15/10)*.

### 4.4 Direction d'une tâche
- Déduite des agents affectés (via leur service, rattaché à une sous-direction ou directement à une direction), à défaut de l'action clé. Aucune colonne dénormalisée.

### 4.5 Charge de travail
- Chaque tâche est rangée dans **une seule catégorie** (terminée, en retard, en cours, autres).
- Une tâche compte **une fois par agent** (quel que soit son rôle) et **une fois par direction**.

### 4.6 Mise à jour rapide d'une tâche (livré cette session)
- Champs modifiables : priorité, statut, avancement.
- L'agent doit être **affecté** à la tâche (tout rôle), sinon refus 403.
- Une tâche **Terminée** n'est plus modifiable, sauf par un **ADMIN**. Contrôle fait côté serveur et côté affichage (cadenas).
- Passer à Terminée force l'avancement à **100 %**.
- Chaque changement de priorité, statut ou avancement est écrit dans l'historique, avec l'auteur, et met à jour `derniere_mise_a_jour_le`.
- « Tâches du jour » = tâches ouvertes en retard ou dont la période (début, échéance) couvre aujourd'hui.

### 4.7 Visibilité des tâches *(fiche 15/10)*
- Aujourd'hui (`visiblePar`) : ADMIN, DG et RD voient tout ; les autres voient leurs tâches.
- Décision de principe : ouvrir la lecture à tous (liste, matrice, calendrier, Gantt) via un réglage « Visibilité des tâches » (Restreinte par défaut, Direction, Toutes). **Trois questions restent ouvertes** : ouvrir à tous ou à la direction ? budget réservé à qui ? justifications de retard visibles de tous ?

### 4.8 Validation de la clôture d'une tâche *(fiche 15/10)*
- Réglage **désactivé par défaut**. Activé : la tâche reste « En cours » (99 % maximum) jusqu'à validation par le supérieur ; circuit valider, refuser, resoumettre ; relance planifiée ; contrôle à la clôture d'exercice.

### 4.9 Sous-tâches (F4b) *(fiche 15/10)*
- Plus d'agent désigné sur les sous-tâches : l'essentiel est de voir ce qui est réalisé.
- Validateur = chaîne hiérarchique complète (chef de service, sous-directeur, directeur, DG). Validation par le supérieur puis coche par le responsable.

### 4.10 Courrier *(fiche 15/10)*
- Instruction « Archiver » ou « Pour information » seule : courrier **Classé**, sans diligence.
- Un chef de file par imputation, avis et copies attachés à la diligence ; clôture en cascade jusqu'à « Traité ».
- Réponse : circuit de visa réglable (toute la chaîne ou DG seul). Annulation : immédiate en brouillon ou sans visa, sinon soumise à l'accord du **dernier visa** (ADMIN et DG peuvent trancher).
- Renvoi : au supérieur (diligence réimputée) ou au DG / imputeur (courrier de nouveau « À imputer »).
- **Confidentialité** : contenu visible seulement des personnes désignées (chaîne d'imputation, avis, copie, circuit de visa), de l'ADMIN, du DG et de l'enregistreur. Le supérieur non désigné voit le suivi, pas le contenu.
- Libellés : « Traitée » (diligence), « Traité » (courrier), « Classé » (archive).

### 4.11 Synthèse IA *(fiche 15/10)*
- Clé API dans `.env`. Jamais de noms, objets ou titres envoyés : seulement des totaux.

### 4.12 Dossier agent *(fiche 15/10)*
- Voir et télécharger : ADMIN, DG et l'agent. Ajouter ou retirer un document, modifier les informations administratives : ADMIN seul. Chaque consultation est journalisée.

---

## 5. Règles d'interface (à respecter dans toutes les pages)

- Tableaux de liste : conteneur `support-ticket-table` (ou `recent-table overflow-x-auto custom-scrollbar`), `id="basic-6"`, personnes en avatars, boutons d'action de 30 px avec infobulle.
- Plusieurs tableaux sur une page : `id="basic-6"` partout, identification par `data-nom` dans le JS.
- **Aucun `<style>` ni `<script>` inline** : CSS et JS dans `public/assets/`.
- Classes limitées à celles du template. Inexistantes : `btn-light-*`, `badge-light-danger`, `l-line-danger`, `title-line-danger/secondary`, `progress-bar bg-danger/secondary`.
- Dans un `<td>` tronqué : `max-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap` avec `table-layout:fixed`.
- Recherche dans toute liste de choix d'agent. En-têtes de page en bleu, design doux.

---

## 6. Points de vigilance et incohérences à traiter

1. **Validation de clôture contournable.** Ma mise à jour rapide permet de passer une tâche directement en « Terminée ». Si le réglage de validation de clôture est activé, il faut passer par le circuit de la fiche 15/10. À aligner avant la mise en service de « Mes tâches ».
2. **Visibilité non contrôlée sur le détail.** La page Détails d'une tâche ne vérifie pas `visiblePar`, ce qui reproduit l'incohérence déjà signalée. À traiter avec la décision du §4.7.
3. **Fichiers de base probablement périmés.** Les copies de modèles et de vues présentes dans le contexte de cette session semblent antérieures à la fiche du 15/10 (par exemple `Tache` sans champs de validation de clôture). Les fichiers livrés cette session (`charge_travail.blade.php`, `_charge_taches.blade.php`, `mes_taches`, `tache_details`) doivent être **comparés** à tes versions actuelles avant d'être copiés.
4. **Champs supposés** dans `tache_details.blade.php` : `exercice->libelle/annee`, `service->nom/libelle`, `user->name`, `situationTemporelle->couleur`. À confirmer.
5. **Pagination de Charge de travail** : style appliqué par JS (classes Bootstrap), non identique à celui de `support-ticket-custom.js`. À valider en navigateur.
6. **Lien « Détails »** depuis la Liste : à ajouter dans `tache.blade.php`.

---

## 7. Reste à faire (ordre suggéré)

1. Tester Mes tâches et Détails, régler les points 1 et 2 du §6.
2. Trancher la visibilité des tâches (3 questions du §4.7).
3. Pages du sous-menu Tâches : Liste (`tache-list`), Timeline, Calendrier, Kanban, vue panoramique, puis Matrice, Gantt, Cartes, Arborescence.
4. Moteur d'alertes des tâches, puis notifications email et SMS.
5. Décisions courrier en attente : diligence « traitée » avec actions non faites, réponse signée obligatoire, retrait pour correction d'une réponse soumise.
6. OCR du courrier arrivée (en pause) ; pages d'erreur 404, 419, 500.
7. Recette complète, puis F11 (règles DRH) et fin de F13.

---

## 8. Pièges techniques connus

- jQuery peut ne pas être chargé quand les scripts inline s'exécutent : lier les événements en JS natif.
- `@json(...)` de Blade se casse sur les virgules : calculer dans `@php` puis `@json($variable)`.
- Le thème force certains `<span>` en ligne : écrire les composants en `<div>` avec `display:… !important`.
- Le thème force des styles sur le dernier `<li>` de la cloche.
- Deux `php.ini` sous WAMP (CLI et Apache) : les deux doivent contenir `curl.cainfo` et `openssl.cafile`.
- Tout nouveau modèle journalisé doit être déclaré dans le morph map.
- `data-code` en `data-*` : jQuery ne convertit pas `"005"` en nombre (la comparaison reste valide).
