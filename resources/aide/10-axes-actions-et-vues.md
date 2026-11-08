---
titre: Axes, actions et vues des tâches
icone: fa-solid fa-diagram-project
couleur: bleu
resume: La structure de la feuille de route (axes, actions) et les vues Tableau matrice, Calendrier et Gantt.
routes: feuille_de_route.vue_ensemble, feuille_de_route.charge_travail
ordre: 4
---
La feuille de route est organisée en **quatre niveaux**, du plus large au plus précis :

1. l'**axe** (une grande orientation stratégique) ;
2. l'**action clé** (un grand objectif de l'axe) ;
3. l'**action mesurable** (un résultat que l'on peut vérifier) ;
4. la **tâche** (le travail concret, avec un responsable et une échéance).

Chaque niveau a sa page dans le menu **Feuille de route**, et les tâches se lisent en plusieurs vues.

## Les axes

{routes: feuille_de_route.axe}

La page **Axes** liste les axes de l'exercice avec leur code, leur libellé et le résultat attendu.

1. Choisissez l'**exercice** en haut de la page.
2. Cliquez sur une ligne pour voir ce que l'axe contient.
3. Avec le bouton d'ajout, l'administrateur et la Direction Générale créent un axe ; le crayon le modifie et la corbeille le supprime.

> **Attention** Supprimer un axe qui contient déjà des actions est refusé ou demande confirmation : déplacez ou supprimez d'abord ce qu'il contient.

## Les actions clés

{routes: feuille_de_route.action_cle}

La page **Actions clés** liste les actions rattachées à un axe, avec la direction qui les porte. Utilisez les filtres pour la limiter à un axe ou à une direction.

> **Astuce** Pour ajouter une action clé, choisissez d'abord l'axe : le formulaire ne propose ensuite que ce qui lui correspond.

## Les actions mesurables

{routes: feuille_de_route.action_mesurable}

La page **Actions mesurables** détaille chaque action clé en résultats vérifiables. Les tâches sont toujours rattachées à une action mesurable : c'est elle qui relie une tâche à son action clé et à son axe.

Les filtres en cascade (axe, puis action clé) restreignent la liste.

## Le tableau matrice

{routes: feuille_de_route.tache.matrice}

**Feuille de route › Tâches › Tableau matrice** montre toute la hiérarchie dans un seul tableau : axe, action clé, action mesurable, puis les tâches de chaque action.

- Lisez une ligne de gauche à droite pour savoir **à quoi se rattache** une tâche.
- Les colonnes d'avancement, de budget et d'échéance permettent de comparer les tâches d'une même action.
- Les filtres (exercice, direction, retards) s'appliquent comme dans la liste.

> **Astuce** C'est la meilleure vue pour **vérifier la cohérence** de la structure avant une réunion ou une importation.

## Le calendrier

{routes: feuille_de_route.tache.calendrier}

**Feuille de route › Tâches › Calendrier** place les **échéances** sur un calendrier mensuel.

1. Naviguez de mois en mois avec les flèches.
2. Chaque tâche apparaît le jour de son échéance, avec la couleur de son état (rouge : en retard, bleu : en cours, vert : terminée…).
3. Cliquez sur une tâche pour ouvrir sa fiche.

> **À savoir** Utilisez cette vue pour repérer les **jours chargés** et répartir les échéances.

## Le diagramme de Gantt

{routes: feuille_de_route.tache.gantt}

**Feuille de route › Tâches › Diagramme de Gantt** représente chaque tâche par une **barre** sur une ligne de temps, du début à l'échéance.

- La longueur de la barre est la durée prévue ; la partie remplie est l'avancement.
- Les barres qui se chevauchent montrent les tâches menées en parallèle.
- Une barre qui dépasse la date du jour sans être terminée correspond à un retard.

> **Astuce** Filtrez par direction ou par axe pour obtenir un Gantt lisible : un diagramme avec toutes les tâches est trop dense.
