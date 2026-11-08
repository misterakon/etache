---
titre: Utilisateurs et profils
icone: fa-solid fa-user-shield
couleur: violet
resume: Créer les comptes, les relier aux agents et choisir le profil d'accès de chacun.
roles: ADMIN, DG
routes: utilisateur.*, parametre.profil_acces, parametre.profil_acces.*
ordre: 12
---
Le menu **Utilisateurs** gère qui peut se connecter et ce que chacun voit.

## Profils d'accès

{routes: parametre.profil_acces, parametre.profil_acces.*}

La page **Profil d'accès** liste les profils de l'application :

| Profil | Ce qu'il permet, en résumé |
|---|---|
| Direction Générale | Tout voir, décider des demandes, piloter |
| Responsable de direction | Suivre sa direction, décider des demandes de ses équipes |
| Responsable de tâche | Suivre et mettre à jour les tâches dont il est responsable |
| Administrateur | Tout configurer : exercices, comptes, réglages |
| Service courrier | Enregistrer et suivre le courrier |
| Agent | Voir et mettre à jour ses propres tâches |

Le profil détermine les rubriques du **menu** et les données visibles.

## Comptes utilisateurs

{routes: utilisateur.*}

La page **Comptes utilisateurs** liste les comptes avec leur agent, leur profil et leur état.

1. Cliquez sur le bouton d'ajout, choisissez l'**agent** concerné, son **profil** et saisissez son adresse e-mail.
2. Le compte reçoit un **mot de passe provisoire** : transmettez-le à l'agent, qui devra le changer à sa première connexion.
3. Le crayon modifie le profil et l'état du compte (**actif** ou non) ; la corbeille supprime le compte.

> **Attention** Préférez rendre un compte **inactif** plutôt que de le supprimer : l'historique des actions reste attribué à son auteur.

> **À savoir** À sa première connexion, un agent voit la **visite guidée** de l'application ; il peut la relancer depuis le bouton « ? ».
