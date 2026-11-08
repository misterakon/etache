---
titre: Listes de référence
icone: fa-solid fa-list-ul
couleur: bleu
resume: Les listes qui alimentent les menus déroulants : types d'agent, fonctions, emplois, priorités, statuts, alertes…
roles: ADMIN, DG
routes: parametre.type_agent, parametre.famille_emploi, parametre.emploi, parametre.role_affectation, parametre.priorite, parametre.statut_execution, parametre.situation_temporelle, parametre.canal_notification, parametre.regle_alerte, dgepn.fonction
ordre: 14
---
Le menu **Paramètres** contient des **listes de référence** : leurs valeurs alimentent les menus déroulants de toute l'application. Elles fonctionnent toutes de la même façon : bouton **Ajouter** en haut du tableau, crayon pour **modifier**, corbeille pour **supprimer**.

> **Attention** Une valeur déjà utilisée (par exemple un statut porté par des tâches) ne peut pas être supprimée sans précaution. **Modifiez** son libellé plutôt que de la supprimer.

## Type agent

{routes: parametre.type_agent}

Catégories d'agents (par exemple fonctionnaire ou contractuel). Le type est affiché sur la fiche de l'agent.

## Fonctions

{routes: dgepn.fonction}

Les fonctions exercées dans l'administration. Une fonction est choisie lors de l'**affectation d'un agent à un poste**.

## Famille d'emploi

{routes: parametre.famille_emploi}

Le regroupement des emplois par grande famille de métiers. À renseigner **avant** les emplois : chaque emploi est rattaché à une famille.

## Emploi

{routes: parametre.emploi}

Les emplois de l'administration, rattachés à une famille. L'emploi apparaît dans la carrière de l'agent.

## Rôle d'affectation

{routes: parametre.role_affectation}

Les rôles qu'un agent peut tenir sur une tâche : **responsable**, pilote, contributeur. Le rôle de responsable sert à savoir qui rend des comptes ; il ne doit pas être supprimé.

## Priorité

{routes: parametre.priorite}

Les niveaux de priorité d'une tâche (par exemple haute, moyenne, basse), affichés sur les cartes et dans les listes prioritaires du tableau de bord.

## Statut d'exécution

{routes: parametre.statut_execution}

Les états d'avancement d'une tâche : **non démarrée, en cours, terminée, bloquée, suspendue, annulée**. Chaque statut a sa **couleur** utilisée dans les graphiques.

> **Attention** Les statuts sont utilisés par les calculs du pilotage : ne changez pas leur sens.

## Situation temporelle

{routes: parametre.situation_temporelle}

La position d'une tâche dans le temps (à venir, en cours de période, en retard…), calculée automatiquement à partir des dates.

## Canal de notification

{routes: parametre.canal_notification}

Les moyens par lesquels les alertes sont transmises (par exemple la notification dans l'application ou l'e-mail). On peut en activer ou désactiver.

## Règle d'alerte

{routes: parametre.regle_alerte}

Les règles qui déclenchent une alerte : à partir de quel délai avant l'échéance, pour quel profil et par quel canal. Ajustez-les pour éviter les alertes inutiles.
