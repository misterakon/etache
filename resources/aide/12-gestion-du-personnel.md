---
titre: Gestion du personnel (RH)
icone: fa-solid fa-address-book
couleur: vert
resume: Organigramme interactif, tableau de bord RH et outils de suivi des fiches, comptes et dossiers.
roles: ADMIN, DG, RD
routes: rh.*
ordre: 9
---
Cette partie complète la rubrique **DGEPN** : elle sert à suivre le personnel dans son ensemble, au-delà de la fiche d'un agent.

> **Attention** Les dossiers du personnel contiennent des informations sensibles : l'accès est réservé aux profils habilités et chaque consultation est enregistrée dans le journal d'activité.

## Organigramme interactif

{routes: rh.organigramme}

La structure en arbre : directions, sous-directions, services et agents en poste.

- Cliquez sur un nœud pour l'ouvrir ou le refermer.
- Cliquez sur un agent pour ouvrir sa **fiche**.
- Les postes vacants ou occupés par intérim sont signalés.

## Tableau de bord RH

{routes: rh.tableau_de_bord}

Une vue chiffrée : effectifs, répartition par direction et par type d'agent, fiches à compléter, alertes sur les dossiers et retraites prévues.

## Qualité des fiches

{routes: rh.qualite, rh.qualite.*}

Repère les **fiches incomplètes** (date de naissance, genre, prise de service…) et indique pour chaque agent ce qu'il reste à renseigner. Le bouton d'export en donne la liste.

> **Astuce** Commencez par les agents qui ont des tâches en cours : leur fiche est la plus consultée.

## Doublons

{routes: rh.doublons}

Détecte les agents **enregistrés deux fois** (même nom, même matricule…). Comparez les deux fiches avant toute fusion ou archivage.

## Comptes des agents

{routes: rh.comptes, rh.comptes.*, rh.mot_de_passe}

Montre quels agents ont un **compte d'accès**, actif ou non, et permet de **télécharger** la liste des comptes. Les mots de passe provisoires se gèrent depuis cette page.

## Import / export Excel

{routes: rh.import, rh.import.*}

1. **Modèle** : téléchargez le classeur à remplir.
2. **Import** : déposez le fichier ; il est contrôlé avant d'être enregistré.
3. **Export** : extrait la liste du personnel en Excel.

## Charge de travail

{routes: rh.charge}

Pour chaque agent : tâches ouvertes, retards, diligences et actions, avec le **seuil de surcharge**. Utile avant d'affecter une nouvelle tâche.

## Validité des dossiers

{routes: rh.dossiers}

Suit les **documents du dossier** (pièces d'identité, diplômes, contrats…) qui expirent ou ont expiré. Une alerte est envoyée avant la fin de validité, selon le délai réglé dans les Réglages.

## Agents archivés

{routes: rh.archives}

Les agents **partis ou archivés**, avec le motif et la date. Un agent archivé n'apparaît plus dans les listes courantes, mais son historique est conservé.
