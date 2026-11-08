# e-Tâche : refonte des pages du menu « Tâches »

Point d'étape au 19/10/2026. Périmètre : les pages du menu Tâches (Liste, Matrice, Kanban, Calendrier, Gantt, Mes tâches) et la fiche d'une tâche.

> **Rien n'a été exécuté côté PHP** (pas de PHP dans l'environnement de travail). Les vues Blade et le contrôleur sont à tester chez vous. Seuls les JS ont été testés dans un navigateur réel (voir §4).

---

## 1. Avancement

| Page | Menu | Statut | Fichiers livrés |
|---|---|---|---|
| Mes tâches | `tache_mes_taches` | Livré, **validé par vous** (retouches à venir) | `mes_taches.blade.php`, `mes-taches.js`, `mesTaches()`, `majRapide()` |
| Liste des tâches | `tache` | Livré, **à tester** | `tache-list.blade.php` |
| Détails d'une tâche | `tache.details` | Livré, **repris par vous à la main** | `tache-details.blade.php`, `tache-details.js`, `detailsTache()` |
| Tableau matrice | `tache_matrice` | Livré, **à tester** | `tache_matrice.blade.php` |
| Kanban | `tache_kanban` | Livré, **à tester** | `kanban.blade.php`, `tache-kanban.js` |
| Calendrier | `tache_calendrier` | Livré, **à tester** | `tache_calendar.blade.php`, `tache-calendar.js` |
| Diagramme de Gantt | `tache_gantt` | Livré, **à tester** | `tache_gantt.blade.php`, `tache-gantt.js` |
| Cartes, Arborescence, Timeline, Vue synthétique | `tache_cartes`, `tache_arborescence`, … | **Non faits** (première génération rangée dans `archive_ne_pas_utiliser/`, à ne pas utiliser) | — |

Éléments communs : `_entete.blade.php` (fil d'Ariane), `_filtres_taches.blade.php` (barre de filtres), partiel existant `feuille_de_route._avatars`.

---

## 2. Fonctionnalités par page

### Mes tâches
- Colonne de gauche : To Do List, Toutes, Terminées, En attente, En cours, Annulées, avec compteurs.
- **To Do List** : timeline (Basic Timeline) des tâches du jour. Bouton « Mettre à jour » sur chaque tâche.
- **Toutes** : tableau. Autres onglets : affichage liste par défaut.
- **Modale de mise à jour** : priorité, statut d'exécution, avancement (curseur).

### Liste des tâches
- 4 cartes de synthèse : budget (total et tâches terminées), avancement moyen / taux de réalisation / tâches en retard, agents mobilisés (avec avatars), total des tâches par groupe de statut.
- Onglets : **Tout** (tableau avec Détails, Modifier, Supprimer), **En cours**, **En attente**, **Terminées** (cartes).
- Bouton « Ajouter une tâche » (formulaire existant `form_tache`).

### Détails d'une tâche (3 onglets)
- **Résumé** : bloc « Informations de la tâche » (tâche, axe, action clé, action mesurable avec leur nombre de tâches, budget, indicateur, livrable, observations : texte complet, sans troncature), résumé avec graphique circulaire (temps écoulé et avancement), équipe, évolution de l'avancement (courbe), activité récente.
- **Équipe** : une carte par affectation (rôle, tâches terminées / en cours / en retard de l'agent).
- **Activité** : historique des modifications et alertes envoyées.

### Tableau matrice
- Hiérarchie Axe › Action clé › Action mesurable › Tâche sur un seul tableau, **cellules fusionnées** (`rowspan`), texte complet.
- Colonnes : responsables, échéance, avancement, statut (en rouge si retard).

### Kanban
- Une colonne par statut d'exécution, compteur par colonne.
- **Glisser-déposer natif** (sans jKanban) : le dépôt change le statut. Retour de la carte en cas de refus.
- Cartes : code (lien vers la fiche), libellé, avancement, échéance, retard, responsables.

### Calendrier
- Chaque tâche est un événement étalé de la date de début à l'échéance. Couleur du statut, rouge si en retard.
- Vues Mois, Semaine, Liste ; textes en français ; filtre par statut (cases à cocher) ; clic = fiche.
- Les tâches sans date sont comptées et signalées, pas affichées.

### Gantt
- Une barre par tâche (début → échéance), triée par date de début, couleur du statut, ligne « Aujourd'hui ».
- Infobulle : code, statut, libellé, dates, avancement. Clic = fiche. Les tâches sans date sont exclues.

### Filtres communs (Matrice, Kanban, Calendrier, Gantt)
Recherche (code, libellé), axe, direction, statut, « en retard ». Moteur `Tache::filtrer()`.

---

## 3. Règles de gestion

### Statuts et retard
- Codes de statut : `001` Non démarrée, `002` En cours, `003` Bloquée, `004` Suspendue, `005` Terminée, `006` Annulée.
- **En retard** : échéance passée et statut ni Terminée ni Annulée (`Tache::estEnRetard()`).
- Groupes d'affichage : *En attente* = Non démarrée + Bloquée + Suspendue ; *En cours* ; *Terminées*. Les annulées ne figurent que dans « Toutes ».
- **Tâches du jour** (Mes tâches) : non terminées et non annulées, soit en retard, soit dont la période couvre aujourd'hui (début ≤ aujourd'hui ≤ échéance).

### Mise à jour d'une tâche (modale de Mes tâches et Kanban)
- Champs modifiables : priorité, statut d'exécution, avancement (0 à 100).
- Droit de modifier : l'**administrateur**, ou l'**agent affecté** à la tâche (quel que soit son rôle d'affectation). Sinon refus (403).
- **Tâche terminée** : verrouillée pour l'agent, **seul l'administrateur** peut la modifier (cadenas côté écran, refus côté serveur).
- Passer en **Terminée** force l'avancement à **100 %**.
- Chaque changement de priorité, statut ou avancement est écrit dans l'**historique** de la tâche (valeurs lisibles, auteur, date) et met à jour « dernière mise à jour ».

### Visibilité et périmètre
- **ADMIN, DG, RD** voient toutes les tâches ; les autres uniquement les leurs (`Tache::visiblePar`). Appliqué à la Liste et, depuis la correction du jour, à Matrice, Kanban, Calendrier et Gantt.
- Exercice : ces pages affichent l'exercice par défaut, comme la Liste (paramètre `exercice_id` accepté).
- Kanban : une carte n'est déplaçable que si l'utilisateur est administrateur ou affecté, et si la tâche n'est pas verrouillée.

### Affichage
- Budget : affichage seul du `budget_estime` par tâche (en FCFA). **Aucun calcul de consommation** : l'application n'enregistre pas de dépenses réelles.
- Textes de la fiche et de la matrice : jamais tronqués. Dans les listes, libellés longs tronqués avec infobulle (modèle de la page Tâches).
- Évolution de l'avancement : reconstituée depuis l'historique des modifications.

---

## 4. Vérifications effectuées

| Élément | Vérification |
|---|---|
| JS Kanban, Calendrier, Gantt | Testés dans Chromium sans interface (ApexCharts 7.6.1, FullCalendar 6.1.21 depuis npm) : déplacement, POST (statut, avancement, jeton CSRF), verrouillage, retour arrière sur 403, rendu en français, barres du Gantt |
| `tache-details.js`, `mes-taches.js` | Syntaxe vérifiée seulement |
| Vues Blade et contrôleur | Équilibre des balises et blocs `@if` / `@foreach` seulement ; **non exécutés** |

Un bug a été trouvé et corrigé par ces tests : au refus du serveur, la carte du Kanban ne revenait pas à sa place.

---

## 5. Points d'attention et décisions en attente

1. **Circuit de validation de clôture** (fiche de reprise : réglage « Clôture des tâches », désactivé par défaut, tâche bloquée à 99 % jusqu'à validation du supérieur, routes `tache.cloture.*`). `majRapide()` passe **directement** en Terminée / 100 % et ne le connaît pas. À aligner si le réglage est activé : je n'ai pas ces fichiers sous les yeux.
2. **Visibilité de la fiche d'une tâche** : `detailsTache()` ne vérifie pas `visiblePar` (incohérence déjà notée dans la fiche de reprise). Dépend de la décision sur l'ouverture des tâches en lecture (3 questions en attente : ouvrir à tous ou à la direction, budget réservé, justifications visibles).
3. **Kanban sur mobile** : le glisser-déposer natif ne fonctionne pas au doigt. Alternative possible : liste déroulante de statut sur la carte.
4. **Scripts de démonstration retirés** (`custom-project.js`, `stock-prices.js`, `common-project-stat.js`, `fullcalendar-custom.js`…) car ils initialisent des graphiques factices. À remettre si un onglet ou un comportement du gabarit ne répond plus.
5. **Graphique « Project Cost Performance »** supprimé de la Liste (données factices, pas de dépenses réelles).
6. **Pièces jointes** : aucune table de fichiers ; l'onglet « Attachment » du gabarit n'est pas repris.
7. **Évolution de l'avancement** : seules les mises à jour faites via la modale ou le Kanban l'alimentent. Le formulaire complet d'édition (`storeTache`) n'écrit pas dans l'historique.

## 6. Reste à faire

- Tester les 7 pages sur vos données et remonter les écarts page par page.
- Trancher les points §5.1 et §5.2.
- Pages restantes du menu : Cartes, Arborescence, Timeline, et vue synthétique (charge de travail non touchée).
- Retouches annoncées sur Mes tâches.
