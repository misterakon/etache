---
titre: Organisation et personnel
icone: fa-solid fa-sitemap
couleur: vert
resume: Directions, sous-directions, services, personnel, organigramme et fiche d'un agent.
roles: ADMIN, DG, RD
routes: dgepn.*
ordre: 8
---
La rubrique **DGEPN** décrit l'organisation : les **directions centrales**, les **sous-directions** et les **services**, ainsi que le **personnel** rattaché.

## Directions centrales

{routes: dgepn.direction}

La liste des directions de la DGEPN, avec leur code et leur nom. L'administrateur peut en **ajouter**, **modifier** ou **supprimer**. Une direction contient des sous-directions et des services.

> **Attention** Une direction qui porte déjà des actions ou des agents ne peut pas être supprimée sans précaution : vérifiez d'abord son contenu.

## Sous-directions

{routes: dgepn.sous_direction}

Les sous-directions, avec la direction dont elles dépendent. Choisissez la **direction** à la création : la sous-direction y est rattachée.

## Services

{routes: dgepn.service}

Les services, rattachés à une sous-direction (ou directement à une direction). Les agents sont affectés à un service.

## Personnels

{routes: dgepn.personnel, dgepn.personnel.*}

La liste de tous les agents : nom, fonction, matricule, service, compte actif ou non.

1. Cherchez un agent par son nom ou son matricule, ou filtrez par direction.
2. Cliquez sur son nom pour ouvrir sa **fiche**.
3. L'administrateur peut **ajouter un agent** et **affecter** un agent à un poste.

## La fiche d'un agent

{routes: dgepn.personnel.fiche}

Elle contient :

- l'**identité** (nom, fonction, matricule, type d'agent, compte actif ou non) ;
- quatre indicateurs : **Tâches**, **En cours**, **En retard** et **Terminées**, avec la part de chacun ;
- les informations d'**état civil et de contact**, l'**affectation**, la **carrière** (grade, classe, échelon) et le **dossier** ;
- les onglets **Tâches**, **Activité récente**, **Alertes reçues** et **Compte**.

**Affecter à un poste** (administrateur) nomme l'agent à une fonction avec une date de début, ou y met fin. **Modifier la fiche** ouvre le formulaire d'identité ; chaque agent peut modifier **ses propres** coordonnées.

> **À savoir** Les informations sensibles sont réservées aux profils habilités, et chaque consultation du dossier personnel est enregistrée.

## Organigramme

{routes: dgepn.responsable}

L'organigramme des **responsables en poste** : qui dirige chaque direction, sous-direction et service, avec la période de son mandat. Un responsable de direction doit avoir un **mandat en cours** pour agir comme tel.

## La charge de travail

La charge d'un agent additionne ses **tâches ouvertes**, ses **diligences** et ses **actions** de traitement en cours. L'onglet « Agents et charge » du Pilotage les classe du plus chargé au moins chargé et signale les agents en **surcharge** (seuil réglé dans Réglages › Dossier du personnel).
