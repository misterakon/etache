# e-Tache (DGEPN) — Point d'avancement

**Date : 19 octobre 2026**
**Périmètre : ce qui a été réalisé dans cette conversation.** Les autres modules du projet (courrier, imputation, etc.) ne sont pas couverts ici.

## Niveau de vérification

- Le code a été contrôlé par des vérifications de syntaxe (PHP, JS, balises Blade) et des simulations.
- Seul ce que l'utilisateur a essayé lui-même est validé en conditions réelles : le journal des échéances, le tableau de bord et les écrans qu'il a commentés.
- Le reste doit être confirmé par la recette.

---

## 1. Fonctionnalités livrées

### Pilotage des tâches
| Écran | Contenu |
|---|---|
| Liste des tâches | 5 onglets (Tout, En cours, En attente, Terminées, Diligence), cartes colorées, pagination par 9, **recherche** (code, libellé, statut, date, budget, responsables ; sans accents), carte **Budget** en anneau |
| Matrice | Blocs dépliables par axe, tableau avec pile d'avatars |
| Calendrier | FullCalendar, panneau latéral (filtres, prochaines échéances, retards) |
| Gantt | Diagramme maison, zoom jour / semaine / mois, flèches de dépendances |
| Mes tâches | Cartes colorées, jauge d'avancement, mise à jour rapide |
| Détails d'une tâche | Indicateurs, frise hiérarchique, équipe, onglet Activité |
| Suivi de l'exécution | Anciennement « Charge de travail » : tuiles, listes cliquables, filtres exercice / direction |
| Transversal | Sélecteur d'exercice sur toutes les pages, palette de couleurs unique |

### Tableau de bord
- 6 compteurs : en attente, terminées, en cours, en retard, diligences, total.
- Tâches prioritaires (slider) et tableau des diligences (à venir ce mois, puis retards).
- Bloc **Budget** : anneau de répartition par catégorie, légende abrégée, budget en retard.
- Bloc **Demandes de justification** : 4 compteurs (en attente, approuvées, refusées, annulées), cliquables vers la page des demandes.
- Design en dégradé diagonal avec motifs, pour les compteurs, le budget et les demandes.
- Version **agent (profil RA)** : mêmes compteurs, budget et chiffres des demandes, limités à ses tâches ; les demandes y sont non cliquables.

### Agents et comptes
- Fiche agent refaite : bandeau, photo, fond personnalisable par CSS, groupes d'informations (Contact, Identité, Affectation), supérieur direct, unités dirigées.
- Formulaire de modification avec photo et changement de mot de passe, selon le profil.
- Formulaire de compte : bouton œil, saisie ou **génération automatique** du mot de passe (12 caractères, copie possible).
- 10 agents de test (script SQL, matricule au format NNNNNNA).

### Organisation et responsables
- Écran **Responsables** : toute la hiérarchie de la Direction Générale aux services, avec le responsable en poste et son état (titulaire, intérim, sans responsable, agent inactif).
- Actions : nommer, mettre fin à un mandat, consulter l'historique, corriger une saisie.
- Table `mandats_responsables` (historique) ; script SQL d'initialisation à partir des profils RD et DG.
- Chaîne hiérarchique d'un agent (chef de service, sous-direction, direction, DG).

### Rôles et accès
- `hasRole()` sur `User` (trait `ADesProfils`), directives Blade `@role`, `@hasrole`, `@hasanyrole`, middleware `role:`.
- Menus filtrés par profil (fait par l'utilisateur).
- Profils : `ADMIN`, `DG`, `RD` (responsable de direction), `RA` (responsable de tâche).

### Journal des modifications
- Chaque modification d'une tâche depuis le formulaire complet est enregistrée : échéance, date de début, priorité, statut, avancement, responsable, pilote, contributeurs.
- L'onglet Activité affiche l'ancienne et la nouvelle valeur, l'écart en jours pour une échéance, et l'auteur.

### Justification de retard
- Demande de justification ou de report, décision, page **Demandes à valider**.
- Compteurs sur le tableau de bord ; filtre `?statut=` sur la page des demandes.
- Droits de visibilité communs à la page et au tableau de bord.

### Alertes
- Règles d'alerte avec **destinataires** (colonne JSON) et canaux.
- 10 règles saisies par script SQL (J-30 à J+30).
- Catalogue de toutes les alertes possibles (échéance, début, risque, statut, inactivité, dépendances, données, synthèses, système).
- Canal SMS inactif pour le moment.

### Outils transverses
- **Select2 automatique** : ajouter la classe `select2-modal` ou `select2-page` à un `<select>` suffit.
- Proposition de design pour l'**en-tête** : dégradé à trois niveaux avec motif.

---

## 2. Règles de gestion

| Sujet | Règle |
|---|---|
| **Exercice affiché** | Celui de l'année en cours et actif ; sinon l'exercice actif ; sinon tout |
| **Catégories de tâches** | Chaque tâche compte une seule fois : retard, puis terminée ou annulée, puis en cours, puis en attente |
| **Fiche agent : administrateur** | Modifie tous les champs, y compris la photo |
| **Fiche agent : l'agent lui-même** | Modifie sa photo, son email, son téléphone, son lieu d'habitation et son mot de passe (mot de passe actuel obligatoire, 8 caractères minimum). Les autres champs sont en lecture seule |
| **Fiche agent : autres utilisateurs** | Accès refusé (403) à la lecture des données et à l'enregistrement |
| **Création d'un agent** | Réservée à l'administrateur |
| **Règle appliquée où ?** | Côté serveur, dans le contrôleur : masquer un champ dans la vue ne suffit pas |
| **Responsable d'une unité** | Mandat actif à la date : l'intérim passe avant le titulaire ; un agent inactif est ignoré |
| **Nommer un responsable** | Ferme le mandat précédent la veille de la prise de fonction ; un nouveau titulaire met aussi fin à l'intérim en cours ; rien n'est effacé |
| **Chaîne hiérarchique** | Chef de service, puis sous-direction, puis direction, puis DG ; sans doublon ; l'agent lui-même est retiré |
| **Qui demande une justification** | Le responsable ou le pilote de la tâche, ou l'administrateur ; pas les contributeurs |
| **Quand** | Toute tâche ni terminée ni annulée ; une seule demande en attente par tâche |
| **Deux types de demande** | Justification seule (l'échéance ne change pas) ou demande de report (avec nouvelle date) |
| **Qui décide** | Report de 15 jours ou moins **et** premier report : responsable de direction. Au-delà, ou à partir du deuxième report : DG. L'administrateur peut toujours décider |
| **Demandeur qui dirige** | La demande monte au niveau supérieur : personne ne décide sa propre demande |
| **Refus** | Toujours motivé |
| **Report accepté** | L'échéance change ; le journal garde l'ancienne et la nouvelle date, avec le décideur comme auteur |
| **Visibilité des demandes** | « En attente » = celles que l'utilisateur doit décider ; les autres statuts = celles qu'il a le droit de voir (administrateur : toutes) |
| **Tableau de bord par profil** | Administrateur, DG, RD : complet. Tous les autres profils : seulement leurs tâches |
| **Agent (RA)** | Voit le budget de ses tâches et les chiffres des demandes sur ses tâches ; pas de lien (la page des demandes lui est réservée) ; compte sans agent lié : zéro tâche et message |
| **Règles d'alerte** | J-30 : responsable. J-15 à J0 : responsable et pilote. J+1 et J+7 : on ajoute le responsable de direction. J+15 et J+30 : on ajoute le DG (J+30 inactive). Application et email |
| **Avancement affiché** | Aujourd'hui : moyenne simple des avancements (annulées comprises). Une proposition de calcul pondéré est en attente de décision |

---

## 3. Reste à faire

| # | Sujet | État |
|---|---|---|
| 1 | **Middlewares sur les routes** | Plan écrit, non appliqué : les menus seuls ne protègent pas, un utilisateur qui connaît l'adresse d'une page peut y entrer |
| 2 | **Notifications** : moteur d'alertes, cloche, email, journal des envois | Non démarré ; prévu avec planificateur **et** déclenchement à la visite |
| 3 | **Avancement global pondéré** et écart avec le plan | Proposition : moyenne pondérée par la durée, annulées exclues, écart avec le plan, taux de réalisation conservé. Attend la décision |
| 4 | **Select2 sur tous les choix d'agent** (formulaire de tâche, compte, affectations) | Outil prêt ; à brancher sur chaque formulaire |
| 5 | **En-tête en dégradé** | Trois variantes proposées (bleu nuit, prune, vert-bleu) ; choix à faire, puis ajout du lien CSS dans le layout |
| 6 | **Canal SMS** | À activer, avec choix d'un fournisseur d'envoi |

---

## 4. Corrections connues à traiter

| Sujet | Détail |
|---|---|
| `Tache::directions()` | Passe seulement par la sous-direction : oublie les services rattachés directement à une direction ou à la DG. À remplacer par `Service::direction()` |
| En-tête, lien « Profil » | Plante sur toutes les pages si le compte n'est lié à aucun agent (`personnel_id` vide) |
| Périmètre du responsable de direction | Voit tout l'exercice ; à limiter à sa direction |
| Boutons de modification des tâches | Visibles pour le profil RA alors que les routes seront refusées une fois les middlewares posés |
| Blocs de calcul dans les vues | Les blocs `@php` de Mes tâches et du détail d'une tâche sont à déplacer vers leurs contrôleurs |
| Graphique du tableau de bord | Le graphique `dashboard_12.js` n'est pas branché sur des données réelles |

---

*Document détaillé et conventions du code : voir `reprise_projet_etache.md`.*
