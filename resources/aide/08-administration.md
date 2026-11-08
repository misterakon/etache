---
titre: Exercices et journal
icone: fa-solid fa-calendar-days
couleur: violet
resume: Créer et clôturer les exercices, reconduire l'année suivante, consulter le journal d'activité.
roles: ADMIN, DG
routes: parametre.exercice, parametre.exercice.*, parametre.reconduction, parametre.reconduction.*
ordre: 10
---
Ce chapitre s'adresse à l'**administrateur** et à la **Direction Générale**.

## Les exercices

{routes: parametre.exercice, parametre.exercice.*}

Un **exercice** correspond à une année de la feuille de route. La page **Paramètres › Exercice** liste les exercices avec leur statut (actif ou non), leurs dates et leur mode d'avancement (par exemple « Par sous-tâches »).

1. **Ajouter** crée un nouvel exercice.
2. Le crayon modifie ses dates et son libellé.
3. Le cadenas verrouille ou rouvre l'exercice.

> **À savoir** Les contrôles faits avant la clôture d'un exercice (tâches non terminées, justifications en attente…) se règlent dans **Réglages › Exercice : clôture et reconduction**.

## La reconduction

{routes: parametre.reconduction, parametre.reconduction.*}

**Reconduction** prépare l'exercice suivant à partir de l'exercice en cours : les axes, actions et tâches non terminées peuvent être reportés.

1. Cliquez sur **Reconduction** dans la page Exercice.
2. Choisissez l'exercice source et l'exercice de destination.
3. Sélectionnez ce qui doit être reconduit (affectations, sous-tâches non cochées, échéance pré-remplie…), puis validez.

> **Attention** Vérifiez les dates des tâches reconduites : elles doivent correspondre à la nouvelle année.

## Le journal d'activité

{routes: journal.*}

Le **Journal d'activité** garde la trace des actions importantes : qui a fait quoi, quand. Il ne peut pas être modifié.

- Filtrez par **module**, par **utilisateur** ou par **période**.
- Cliquez sur une ligne pour voir le détail.
- Le bouton d'export extrait les lignes affichées (au maximum le nombre fixé dans les Réglages).

> **Astuce** Consultez-le pour comprendre l'historique d'une décision ou d'une modification.
