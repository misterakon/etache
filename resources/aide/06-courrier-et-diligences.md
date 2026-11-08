---
titre: Courrier et diligences
icone: fa-solid fa-envelope-open-text
couleur: orange
resume: Enregistrer le courrier, l'imputer, traiter les diligences, archiver et paramétrer.
roles: ADMIN, DG, COURRIER
routes: courrier.*
ordre: 7
---
Le **courrier** reçu ou envoyé est enregistré dans l'application. Quand il demande une action, il est **imputé** : une **diligence** est alors créée pour la personne ou le service qui doit agir. Chaque page du menu **Courrier** a sa rubrique ci-dessous.

## Mes courriers à traiter

{routes: courrier.mes}

Cette page liste les courriers **qui vous sont imputés**, avec leur échéance.

1. Ouvrez un courrier pour lire l'objet, le scan et les instructions.
2. Cliquez sur **Prendre en charge** dès réception : c'est votre accusé de prise en charge.
3. Traitez-le, rédigez la réponse si elle est demandée, puis **soldez** la diligence.

> **Astuce** Les courriers urgents ou proches de leur échéance sont signalés : traitez-les en premier.

## Registre - Arrivée

{routes: courrier.arrivee, courrier.show, courrier.doublon, courrier.export}

Le registre des courriers **reçus**.

1. Cliquez sur le bouton d'ajout (**Nouveau courrier**) et renseignez l'expéditeur, l'objet, les dates, le type, l'urgence et le mode de transmission.
2. Joignez le **scan** du courrier : il est obligatoire à l'enregistrement, sauf si l'administrateur a désactivé cette règle.
3. Enregistrez : un **numéro** est attribué automatiquement, selon le format défini dans les Réglages.
4. Depuis la fiche du courrier, **imputez-le** (voir plus bas), classez-le ou archivez-le.

Le bouton **Exporter** produit la liste du registre avec les filtres actifs.

> **Attention** Si un courrier semble déjà enregistré, l'application le signale pour éviter les **doublons**. Vérifiez avant de continuer.

## Imputer un courrier

{routes: courrier.imputer}

L'imputation désigne le ou les destinataires (direction, sous-direction, service ou agent) et fixe une **échéance**. Chaque destinataire reçoit une diligence et une notification. Le paramétrage du courrier propose des **délais de traitement** par défaut.

> **À savoir** Un courrier confidentiel ne peut être réimputé que par le DG si le réglage correspondant est activé.

## Registre - Départ

{routes: courrier.depart, courrier.depart.*}

Le registre des courriers **expédiés**. Chaque courrier envoyé reçoit un numéro **par destinataire**, comme dans le registre papier, avec le lien vers le courrier d'origine et son archivage. Quand une réponse est signée, le courrier départ peut être créé automatiquement (réglage Courrier).

## Tableau des diligences

{routes: courrier.diligences, courrier.diligence.*}

Le travail demandé à chaque destinataire pour les courriers imputés. Une diligence passe par ces états :

| État | Signification |
|---|---|
| À prendre en charge | Imputée, personne ne l'a encore prise |
| En cours | Prise en charge, en traitement |
| En retard | L'échéance est passée |
| Réimputée | Confiée à un autre destinataire |
| Traitée | Terminée |
| Annulée | Abandonnée |

Ouvrez une diligence pour voir l'historique, les **actions** à mener, les réponses et pour **demander un délai** supplémentaire.

> **À savoir** Le responsable et son supérieur sont alertés avant l'échéance et en cas de retard ; les délais d'alerte se règlent dans les Réglages.

## Archives

{routes: courrier.archives}

Les courriers **classés**, avec leur numéro d'archives et leur durée de conservation. Rien n'est jamais supprimé automatiquement : l'échéance de conservation est une **information**. Utilisez la recherche pour retrouver un courrier ancien.

## Paramétrage du courrier

{routes: courrier.parametrage}

Réservé à l'administration : les **types de courrier**, les **niveaux d'urgence** et les **délais de traitement** proposés à l'imputation.

- Ajoutez ou modifiez une ligne avec les boutons de chaque tableau.
- La numérotation, les profils autorisés et les scans se règlent dans les **Réglages › Courrier**.
