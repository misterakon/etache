# e-Tache : cahier des charges fonctionnel des nouvelles fonctionnalités

> Ce document décrit **ce que fera chaque nouvelle fonctionnalité**, avec ses règles, ses droits, ses réglages, sa traçabilité et ses critères de recette.
> L'ordre de livraison et l'état d'avancement sont dans `reprise_etache_lots.md`.

**Légende des statuts de décision**
- **[Décidé]** : validé avec l'utilisateur.
- **[Par défaut]** : proposition retenue sauf avis contraire ; elle sera de toute façon réglable.
- **[À valider]** : information ou décision manquante ; je ne la tranche pas seul.

**Rôles utilisés** : ADMIN (administrateur), DG (direction générale), RD (responsable de direction, selon mandat en cours), RA (responsable de tâche), AGENT (agent avec compte, nouveau profil), DRH (gestion du personnel et des congés), COURRIER (service courrier et secrétariat du DG). Un RD sans mandat agit comme un RA.

---

## 0. Principes transversaux (valables pour toutes les fonctionnalités)

### 0.1 Tout est paramétrable [Décidé]
- Aucune règle « en dur » : chaque règle de gestion est un **réglage** (module, clé, type, valeur par défaut, portée **globale** ou **par exercice**).
- Écran « Réglages » par module : explication, valeur par défaut, bouton « Rétablir ».
- **Journal des modifications de réglages** : qui, quoi, quand, ancienne et nouvelle valeur.
- La valeur par défaut reproduit le comportement actuel : à la mise en ligne, **rien ne change tant que l'admin n'a rien modifié**.
- Garde-fous : verrouillage après usage (ex. format de numérotation du courrier), aperçu d'impact avant validation, valeurs bornées, désactivation plutôt que suppression.
- **Ne se paramètrent jamais** : séparation des rôles, contrôle d'accès des courriers confidentiels, journal d'audit.

### 0.2 Traçabilité [Décidé]
Tout acte significatif est tracé (voir F6). Rien n'est modifiable ni supprimable dans le journal.

### 0.3 Pièces jointes génériques [Décidé]
Un seul mécanisme sécurisé pour tous les fichiers (livrables, courrier, dossier de l'agent, justificatifs RH). Voir F5.

### 0.4 Exports [Décidé]
Un moteur commun : Excel et PDF, filtres de l'écran appliqués, droits respectés, en-têtes réglables. Voir F2.

### 0.5 Droits [Décidé]
Droits par module et, pour le personnel et les RH, **par champ**. Un RA ne voit et n'exporte que ses tâches ; un RD son périmètre ; DG et ADMIN tout.

### 0.6 Instantané quotidien de l'avancement [Décidé]
Chaque nuit, enregistrement de l'état (par tâche, direction et axe : nombre par statut, avancement moyen, retards, retards non justifiés). Il est **nécessaire** aux comparaisons dans le temps (F8, F12). À démarrer tôt pour que l'historique se constitue.

### 0.7 Confidentialité de l'IA [Décidé]
Les données du courrier et des RH ne sont **jamais** envoyées telles quelles à l'API d'IA. Uniquement des agrégats (compteurs), agents anonymisés, sauf décision écrite de la direction.

---

## F1. Annulation d'une tâche par l'administrateur

**Objectif** : permettre de sortir proprement du suivi une tâche devenue sans objet, sans perdre son historique.

**Acteurs et droits** : ADMIN uniquement [Décidé]. Réglage « qui peut annuler » (défaut : ADMIN).

**Règles de gestion**
1. L'annulation exige un **motif** : choix dans une liste paramétrable + commentaire libre obligatoire [Décidé : motif obligatoire].
2. La tâche passe au statut **« Annulée »** (le statut existe déjà dans l'application).
3. Une tâche annulée **sort des retards, des alertes, des échéances proches et des calculs d'avancement** [Par défaut]. Réglage : « une tâche annulée compte-t-elle dans les indicateurs ? » (défaut : non).
4. Elle reste visible via le filtre « Annulées » et dans les exports correspondants ; elle n'est jamais supprimée.
5. Ses sous-tâches et fichiers sont **figés** (consultables, plus modifiables).
6. Avertissement avant validation si d'autres tâches **dépendent** de celle-ci, ou si des demandes de justification ou de report sont **en attente** (elles passent « sans objet »).
7. **Réactivation** possible par l'ADMIN, avec motif : la tâche retrouve son statut précédent [Par défaut].
8. Notification (cloche et email) au responsable, au pilote et au RD de la direction porteuse.
9. Impossible dans un exercice **clos** (voir F7).

**Données ajoutées** : date, auteur et motif d'annulation, statut avant annulation.
**Traçabilité** : événement « annulation » et « réactivation » (qui, quand, motif, statut précédent).
**Écrans** : bouton « Annuler la tâche » (ADMIN) dans le détail ; fenêtre de confirmation avec motif ; badge « Annulée » ; filtre dédié.
**Critères de recette** : une tâche annulée disparaît du tableau de bord (retards, échéances), reste exportable, et un RA ne peut plus la modifier ; l'ADMIN peut la réactiver ; chaque action est dans l'onglet Activité.

---

## F2. Téléchargement des tâches et exports Excel / PDF

**Objectif** : télécharger les listes de tâches par situation (demande du DG) et, plus largement, toute donnée de pilotage.

**Exports de tâches** [Décidé]

| Export | Contenu |
|---|---|
| En retard | Tâches en retard, avec jours de retard |
| Retards non justifiés | En retard sans justification approuvée ni demande en attente |
| Justifiés / en attente de décision | État de la justification |
| En cours | Statut « En cours » |
| Terminées | Terminées, avec date de fin réelle |
| À venir | Non démarrées, et échéances sous 7, 15 ou 30 jours (réglable) |
| Annulées | Avec motif d'annulation |
| Sans responsable | Tâches sans responsable désigné |

**Autres exports** : synthèse par direction, par axe, par agent, par période ; justifications et reports ; charge de travail ; sous-tâches ; matrice complète (même structure que l'import) ; rapport mensuel (PDF). Plus tard : **diligences** (en cours, en retard, soldées), registres du courrier, personnel, congés et absences.

**Colonnes proposées pour la liste des tâches** [Par défaut, réglables] : code, libellé, chemin (axe / action clé / action mesurable), direction porteuse et associées, responsable, pilote, contributeurs, statut, situation, priorité, avancement, début, échéance, jours de retard, état de la justification, nombre de reports, budget, indicateur, livrable (texte), nombre de sous-tâches cochées / total, livrable déposé (oui/non).

**Règles de gestion**
1. Les **filtres actifs de l'écran** (exercice, axe, action clé, direction, porteuse / impliquée, en retard, non justifié) s'appliquent à l'export.
2. **Droits** : l'export ne contient jamais plus que ce que l'utilisateur voit.
3. Excel : en-tête figé, filtres automatiques, mise en forme lisible. PDF : paysage, en-tête (logo, titre, exercice, filtres, date, auteur) et mention de confidentialité réglables.
4. Nom de fichier horodaté et explicite.
5. **Volume** : au-delà d'un seuil réglable, génération en tâche de fond avec notification quand le fichier est prêt.
6. **Chaque export est tracé** (qui, quoi, filtres, nombre de lignes) [Par défaut].
7. Boutons « Télécharger » présents sur chaque liste, avec choix du format.

**Rapport mensuel (PDF)** : page de garde (exercice, mois), chiffres clés, avancement par axe et par direction, tâches terminées dans le mois, retards avec leur justification, échéances du mois suivant, demandes de justification et de report du mois avec décisions. Modèle à adapter si la direction en a un [À valider].

**Réglages** : colonnes par export, en-têtes, logo, mentions, seuils de volume, formats de date.
**Critères de recette** : chaque export correspond exactement à la liste affichée avec les mêmes filtres ; un RA ne peut pas exporter les tâches des autres ; le PDF est lisible en paysage.

---

## F3. Diagramme de Gantt par axe

**Existant** : le Gantt est déjà **groupé par axe** (un groupe repliable par axe, une teinte par axe), filtrable par axe via la barre de filtres, avec zoom jour/semaine/mois, bouton « Aujourd'hui », « Tout déplier / replier » et flèches de dépendance. Seules les tâches datées apparaissent (le nombre de tâches sans date est signalé).

**À ajouter** [À valider : le DG veut-il le résumé ou le détail ?]
1. **Vue « Par axe »** : une ligne par axe, une barre de la plus ancienne à la plus récente date, avancement moyen, nombre de tâches, de retards et de retards non justifiés.
2. **Niveaux emboîtés** : un clic déplie l'axe, puis les actions clés, puis les tâches.
3. **Avancement prévu et réel** par axe (quand l'historique quotidien existe).
4. **Export du Gantt** en image ou PDF, avec les filtres actifs.

**Critères de recette** : la vue « Par axe » est cohérente avec la liste des tâches ; l'export reproduit ce qui est affiché.

---

## F4. Sous-tâches et avancement calculé automatiquement

**Objectif** : suivre ce que le responsable de tâche fait réellement jusqu'à l'échéance, et supprimer la saisie manuelle de l'avancement lorsque le mode est activé (demande du DG).

### Réglage central : « mode d'avancement » [Décidé]
- **Manuel** (défaut) : comportement actuel, rien ne change.
- **Par sous-tâches** : l'avancement est calculé.
- Portée : **par exercice** [Par défaut ; global ou par exercice à confirmer].
- Le changement de mode se fait avec confirmation, **aperçu d'impact** (« N tâches seront recalculées »), trace, et conserve les valeurs existantes, qui sont ensuite recalculées ou saisies selon le nouveau mode.

### Règles de gestion (mode « par sous-tâches »)
1. **Qui gère** : le responsable de la tâche crée, modifie, supprime, coche et décoche ses sous-tâches [Décidé]. Le pilote et les contributeurs : consultation (réglable).
2. **Calcul** : avancement = sous-tâches cochées ÷ total × 100, arrondi à l'entier ; **même poids** pour toutes [Par défaut] ; une colonne de poids existera pour une pondération future.
3. **Statut automatique** limité à trois valeurs [Par défaut] : aucune cochée = Non démarrée ; certaines = En cours ; toutes = Terminée. **Bloquée, Suspendue et Annulée restent manuels** et ne sont jamais écrasées. Décocher une sous-tâche d'une tâche terminée la remet « En cours ».
4. **Tâche sans sous-tâche** : l'avancement reste figé à sa dernière valeur, avec la mention « avancement non calculé » [Par défaut].
5. **Champ d'avancement en lecture seule** dans le formulaire de tâche, la mise à jour rapide est désactivée ; la colonne « Avancement » de l'import est ignorée avec un avertissement.
6. **Après l'échéance** : seuls RD, DG et ADMIN peuvent modifier les sous-tâches [Par défaut, réglable].
7. **Suppression** d'une sous-tâche cochée : confirmation et trace.
8. **Limites réglables** : nombre maximal de sous-tâches par tâche (défaut proposé : 50), commentaire à la coche obligatoire ou non.
9. **Garde-fou contre le contournement** : ajouter des sous-tâches après coup dilue le pourcentage ; chaque ajout est tracé et visible du RD. Option à valider : plafonner les ajouts après un certain avancement [À valider].
10. Fichiers livrables possibles sur chaque sous-tâche (F5).

**Données** : `sous_taches` (tâche, libellé, description, échéance facultative, ordre, cochée, date et auteur de la coche, créateur, poids).
**Écrans** : zone « Sous-tâches » avec cases à cocher dans « Mes tâches » et dans le détail de la tâche (onglet dédié), indicateur « 3/5 » dans les listes, barre d'avancement calculée.
**Impacts à adapter** : liste, matrice, calendrier, Gantt, détail, tableau de bord, Suivi de l'exécution, alertes, exports, import, analyse IA (ils lisent tous le même champ d'avancement : ils n'ont pas à connaître le mode).
**Migration** : les 118 tâches existantes gardent leur avancement jusqu'à ce que le mode soit activé ; aucune valeur n'est faussée.
**Critères de recette** : cocher une sous-tâche met à jour l'avancement et le statut sans rechargement manuel ; décocher le recule ; en mode manuel, rien n'a changé ; chaque action est dans le journal.

---

## F5. Fichiers livrables (tâche et sous-tâche) et pièces jointes génériques

**Objectif** : joindre des preuves de réalisation à une tâche ou une sous-tâche, avec un mécanisme réutilisé partout.

**Règles de gestion** [Par défaut, tout réglable]
1. **Types autorisés** : PDF, Word, Excel, PowerPoint, images, zip. **10 Mo** par fichier, **10 fichiers** par tâche.
2. **Contrôles** : extension et type MIME vérifiés ; nom d'origine conservé pour l'affichage, nom de stockage aléatoire.
3. **Stockage privé** (hors dossier public) : le téléchargement passe par l'application, qui contrôle les droits à chaque accès.
4. **Visibilité** : toute personne qui voit la tâche voit ses fichiers.
5. **Dépôt** : le responsable de la tâche (et le pilote selon réglage) ; **suppression** : l'auteur avant l'échéance, RD, DG, ADMIN ensuite.
6. **Versions** : un nouveau dépôt peut remplacer un fichier en **conservant l'ancienne version** (pas d'écrasement) [Par défaut].
7. Aperçu des PDF et images, téléchargement groupé (zip).
8. **Verrouillage** après clôture de l'exercice et sur une tâche annulée.
9. Indicateur « livrable déposé » dans les listes ; le champ texte « livrable » existant reste le **livrable attendu**.
10. **Sauvegarde** du dossier de stockage incluse dans le plan de sauvegarde.
11. Options à valider : analyse antivirus, quota par exercice.

**Traçabilité** : dépôt, remplacement, suppression et téléchargement tracés.
**Réutilisation** : le même mécanisme sert au courrier scanné (F9), au dossier de l'agent (F10) et aux justificatifs d'absence (F11), avec ses propres types et tailles.
**Critères de recette** : un fichier non autorisé est refusé avec un message clair ; un utilisateur sans droit sur la tâche ne peut pas télécharger le fichier même avec le lien direct.

---

## F6. Traçabilité : journal d'activité unifié

**Existant** : la table `tache_historiques` enregistre les changements champ par champ (échéance, dates, priorité, statut, avancement, responsable, pilote, contributeurs) et alimente l'onglet « Activité » du détail d'une tâche.

**À ajouter** [Décidé] : un journal **d'événements** couvrant tous les modules, sans remplacer l'existant.

| Domaine | Événements tracés |
|---|---|
| Tâches | annulation, réactivation, changement de mode d'avancement |
| Sous-tâches | création, modification, suppression, coche, décoche |
| Fichiers | dépôt, remplacement, suppression, téléchargement |
| Réglages | modification (ancienne et nouvelle valeur) |
| Exercice | clôture, réouverture, reconduction |
| Exports | qui exporte quoi, avec quels filtres |
| Courrier | enregistrement, imputation, réimputation, visa, signature, expédition, consultation d'un courrier confidentiel |
| Personnel | modification de fiche, mouvement, archivage |
| RH | demande, avis, décision, annulation |

**Contenu d'une entrée** : date et heure, acteur (avec le nom conservé tel qu'il était), module, type d'événement, entité concernée et entité parente (ex. la tâche), résumé, détails avant/après. **Aucune donnée médicale** dans le journal.
**Règles** : non modifiable, non supprimable ; durée de conservation réglable.
**Écrans** : onglet « Activité » enrichi (tâche), fiche agent, dossier de courrier ; page **« Journal d'activité »** (ADMIN, DG) avec filtres (période, module, acteur, type) et export.
**Vue « activité réelle »** : pour une tâche, chronologie de ce que fait le RA jusqu'à l'échéance (sous-tâches cochées, fichiers déposés, modifications) [demande du DG]. Indicateur « tâches sans mouvement depuis N jours » (seuil réglable).
**Critères de recette** : toute action des fonctionnalités F1, F4, F5 apparaît avec son auteur et sa date ; une entrée ne peut être ni modifiée ni supprimée.

---

## F7. Clôture d'exercice, verrouillage et reconduction

### Clôture [Décidé]
1. **Contrôle préalable** : liste des tâches non terminées et non annulées, retards non justifiés, demandes de justification ou de report en attente, tâches sans responsable, sous-tâches non cochées. Chaque contrôle est **bloquant ou simple avertissement** (réglable).
2. **Clôture** par l'ADMIN (et le DG selon réglage) avec confirmation ; enregistrement de la date et de l'auteur (colonne `cloture_le`).
3. **Archive Excel** générée à la clôture : matrice complète, liste des tâches, journal de l'exercice ; conservée et téléchargeable.
4. **Verrouillage côté serveur** : plus aucune création, modification ou suppression d'axe, d'action, de tâche, de sous-tâche, de fichier, d'affectation, de justification ou d'import dans l'exercice clos. Lecture, exports et analyses restent possibles. Messages explicites.
5. Les **alertes** cessent pour un exercice clos.
6. **Réouverture** par l'ADMIN uniquement, avec motif, tracée.

### Reconduction [Décidé : DG et ADMIN ; RD pour sa direction à confirmer]
1. Choix de l'exercice source et de l'exercice de destination.
2. Sélection des tâches **non terminées** à reprendre (cases à cocher).
3. **Échéance obligatoire** pour chaque tâche reprise.
4. Options : reprise des affectations ; reprise des sous-tâches non cochées ; **les fichiers ne sont pas repris** [Par défaut].
5. Nouveaux codes générés ; lien conservé avec la tâche d'origine (« reconduite de… ») ; la tâche source garde son état et est marquée « reconduite vers 2027 ».
6. **Libellé d'axe unique par exercice** et non plus dans toute la base [Décidé] (nécessaire pour reprendre les mêmes axes d'une année à l'autre).

**Besoin pour développer** : `SHOW CREATE TABLE exercices`, `ParametreController.php`, vue Exercices.
**Critères de recette** : après clôture, toute tentative d'écriture (formulaire, import, API interne) est refusée côté serveur ; la réouverture rétablit les droits ; la reconduction crée les tâches avec leurs liens.

---

## F8. Synthèses et récapitulatifs (axe, année, période)

**Objectif** : donner au DG une vue de synthèse de la situation, sur tous les volets.

Trois couches **indépendantes**, pour que l'IA ne bloque rien :

| Couche | Contenu | Dépend de l'IA |
|---|---|---|
| **Chiffres et graphiques** | Par axe, direction, année (comparaison d'exercices) et **période libre** : avancement, répartition des statuts, retards, retards non justifiés. Lecture de l'instantané quotidien. | Non |
| **Récapitulatif automatique** | Texte construit sur des **modèles de phrases et des seuils réglables** (ex. « 42 tâches sur 118 sont en retard, dont 40 sans justification »). | Non |
| **Synthèse IA** (dernier lot) | Résumé, points d'attention, déductions, recommandations, limites. | Oui |

**Volets de synthèse** : feuille de route (axe, direction, année, période), activité réelle (rythme de cochage, tâches sans mouvement), justifications et reports (motifs, délais), courrier et diligences (volumes, délais, retards), RH (agrégats), **synthèse pour le DG** (situation sur la période choisie avec comparaison).

**Règles IA** [Décidé] : anonymisation des agents, périmètre et droits de la page, **données agrégées**, limite par heure, cache, mention « générée par une IA, à vérifier », export PDF et « Copier ».
**Réglages IA** : activer ou non, modèle, anonymisation, limite par heure, volets et rôles autorisés, durée du cache.
**État** : l'analyse du Suivi de l'exécution est **déjà développée** ; activation reportée **faute de compte et de crédits API** (voir `reprise_etache_lots.md`, section 4.2).
**Critères de recette** : les chiffres d'une synthèse correspondent aux listes ; la comparaison de deux périodes utilise l'historique quotidien ; sans IA, le récapitulatif automatique fonctionne seul.

---

## F9. Courrier et diligences

**Objectif** : suivre les courriers entrants, leur imputation et les diligences qui en découlent, de l'enregistrement à l'archivage.

### Décisions [Décidé]
- Enregistrement par le **service courrier ou la secrétaire du DG** : **registre unique**, une seule numérotation ; chaque courrier porte « enregistré par » et son **origine** ; avertissement si un courrier de même expéditeur, même référence et même date existe déjà.
- **Plusieurs types de courrier** : liste gérée par l'ADMIN.
- **Signature manuscrite scannée** pour l'instant ; la signature électronique viendra plus tard.

### Circuit
| Étape | Qui | Dans l'application |
|---|---|---|
| 1. Réception et enregistrement | COURRIER | Numéro automatique `ARR-AAAA-NNNN`, date de réception, date et référence du courrier, expéditeur, objet, type, urgence, confidentialité, scan PDF |
| 2. Pré-tri | Cabinet (optionnel) | Étape activable par réglage [Par défaut : désactivée] |
| 3. Présentation au DG | COURRIER, cabinet | Visibilité limitée pour les courriers confidentiels |
| 4. Imputation | DG et personnes désignées | **Responsable principal**, **associés** (copie ou avis), **instruction**, **délai** ; chaque imputation crée une **diligence** |
| 5. Ventilation | COURRIER | Notification et **accusé de prise en charge** (équivalent de la décharge) |
| 6. Réimputation | Directeurs, chefs de service | Cascade vers un agent, avec historique ; renvoi possible avec motif |
| 7. Traitement | Destinataire | Sous-tâches, fichiers, demande de délai (circuit de report existant) |
| 8. Réponse | Hiérarchie, DG | Projet joint, **visa(s)**, **signature scannée** (PDF signé déposé, tracé) |
| 9. Expédition | COURRIER | Numéro `DEP-AAAA-NNNN` lié au courrier d'origine, registre départ |
| 10. Clôture et archivage | COURRIER, archiviste | Diligence soldée ; archivage consultable ; champ « à conserver jusqu'au » |

### Règles de gestion
1. **Diligence = tâche d'un nouveau type**, sans action mesurable : elle hérite des sous-tâches, fichiers, alertes, reports, exports et du suivi de l'exécution [Décidé].
2. Un courrier peut générer **plusieurs diligences** (plusieurs destinataires) et plusieurs réponses.
3. **Statuts de diligence** : à prendre en charge, en cours, en retard, soldée, annulée.
4. **Numérotation** `ARR-2026-0001` / `DEP-2026-0001`, remise à zéro chaque année [Par défaut], format modifiable mais **verrouillé après le premier numéro émis**.
5. **Qui peut imputer** : le DG et les personnes qu'il désigne ; les directeurs réimputent dans leur direction [Par défaut].
6. **Délais** : un délai par défaut selon l'urgence (normal, urgent, très urgent), modifiable à chaque imputation [Par défaut].
7. **Instructions** : modèles paramétrables (pour traitement, pour avis, me proposer une réponse, pour information…).
8. **Archivage** : aucune suppression automatique ; durée de conservation à indiquer [À valider].
9. **Confidentialité** : un courrier confidentiel n'est visible que des personnes désignées ; chaque consultation est tracée.
10. Alertes d'échéance et de retard sur les diligences, comme pour les tâches.

**Écrans** : registre arrivée, registre départ, détail d'un courrier (fil chronologique complet), tableau des diligences (en cours, en retard, soldées), « Mes courriers à traiter ».
**Exports** : registres, diligences par statut, direction, période (demande du DG).
**Graphiques** : courriers arrivés et départs par mois, délai moyen de traitement, diligences en retard par direction.
**IA** : uniquement des compteurs agrégés, jamais l'objet des courriers.
**Réglages** : numérotation, types, urgences et délais, pré-tri, qui impute, modèles d'instructions, classification de confidentialité, conservation.
**À valider avant de coder** : photo d'une page du registre et d'une fiche d'imputation, qui peut imputer exactement, durée d'archivage, format de numérotation définitif.
**Critères de recette** : un courrier imputé crée les diligences correspondantes avec leur délai ; une réimputation conserve l'historique ; un courrier confidentiel est invisible des non-désignés ; la numérotation ne produit ni doublon ni trou.

---

## F10. Gestion du personnel (amélioration du module existant)

**Existant** : fiche agent (matricule, identité, naissance, fonction, email, téléphone, lieu d'habitation, photo, service, type d'agent, emploi, actif), liste, fiche détaillée, lien avec les comptes, les affectations de tâches et les mandats.

### F10.1 Fiabilisation des fiches (Personnel I)
1. **Contrôles de qualité** : matricule unique, email unique, format de téléphone, âge plausible, service obligatoire.
2. **Tableau « fiches incomplètes »** (sans email, matricule, service ou photo), avec export.
3. **Détection des doublons** à la création (même nom, prénom et date de naissance).
4. **Import Excel du personnel** (modèle, prévisualisation, tout ou rien, comme l'import de la matrice).
5. **Journal des modifications de fiche**.
6. **Archivage** d'un agent (« ancien agent ») au lieu de suppression.
7. **Comptes en masse** : création à partir de la liste du personnel, mot de passe provisoire à changer à la première connexion, **profil « AGENT » par défaut** (accès limité à ses propres demandes, soldes et calendrier d'équipe), désactivation automatique au départ.

### F10.2 Historique et organisation (Personnel II)
1. **Mouvements datés** : affectation, mutation, promotion, changement d'emploi, avec date, référence de l'acte et motif ; **frise de carrière** sur la fiche.
2. **Situations administratives** : en activité, détaché, disponibilité, congé de longue durée, retraite, avec dates et acte. La liste des positions suit le statut général (à compléter avec le texte) [À valider].
3. Les anciens mandats et tâches restent rattachés au **bon service à la bonne date**.
4. **Organigramme interactif** avec postes vacants ; distinction poste et agent.
5. **Tableau de bord du personnel** : effectifs par direction, service, type d'agent, emploi ; pyramide des âges ; départs à la retraite prévus.
6. **Dossier de l'agent** : pièces jointes (arrêtés, diplômes, contrats…) avec type, date, validité et droits stricts ; **rappels d'échéance** (fin de contrat, de détachement, d'intérim, de validité) ; compétences et formations ; contacts d'urgence (optionnels).
7. **Lien avec e-Tache** : charge de travail sur la fiche, alerte de sur-affectation (seuil réglable), bouton « réaffecter ses tâches et mandats » au départ d'un agent.

**Confidentialité** : droits **par champ** (téléphone personnel et date de naissance réservés à la gestion du personnel), accès aux dossiers sensibles tracé.
**Réglages** : types de mouvements, situations administratives, types de pièces, droits par champ, seuils de rappel.
**Besoin** : fichier Excel du personnel, liste des agents sans compte.
**Critères de recette** : une fiche incomplète apparaît dans le tableau dédié ; un doublon est signalé à la saisie ; un mouvement de carrière conserve l'ancien service ; aucun agent n'est supprimé, seulement archivé.

---

## F11. Module RH : congés et absences

**Base légale** [Décidé : s'appuyer sur le statut général 2023]
- **Loi n° 2023-892 du 23 novembre 2023** (statut général) et **décret n° 2025-120 du 26 février 2025** (chapitre IV, articles 77 à 107, avantages sociaux).
- Dans la loi : art. 89 **congé annuel de 30 jours calendaires** avec rémunération, exercé selon les nécessités du service ; art. 90 congé de maladie de courte durée de 6 mois sur 12 mois consécutifs, rémunération intégrale ; art. 91 longue durée jusqu'à 36 mois sur proposition du **Conseil de Santé et Sécurité au Travail** (plein traitement 12 mois, puis moitié, puis Commission de Réforme) ; art. 92 congé exceptionnel de maladie jusqu'à 60 mois ; art. 95 à 97 maternité et allaitement, paternité, congé parental, autorisations spéciales d'absence et permissions pour événements familiaux (détail par décret) ; art. 43 avis du Conseil de Santé sur les congés de maladie et la reprise.
- Presse : maternité 6 mois (2 avant, 4 après), paternité 1 mois, congé parental jusqu'à un an.
- **Non vérifié** [À valider avec la DRH] : barèmes des événements familiaux, report et fractionnement du congé annuel, délais, signataires, régime des **contractuels**. À prendre dans le texte du décret et le guide de la DRH. Les règles chargées par défaut sont à faire valider par la DRH.

### Principe : les règles sont des données [Décidé]
Le code contient des mécanismes génériques (plafond glissant, seuil, barème, chaîne de validation) ; chaque règle de la loi devient une ligne de réglage avec sa **source** (article ou décret).

| Famille de réglages | Contenu |
|---|---|
| Régimes | Fonctionnaire (statut 2023), contractuel, autres ; rattachés au type d'agent |
| Types d'absence | Catégorie (congé, absence, en service comme la mission), unité (calendaires, ouvrables, heures), plafond et période de référence (ex. 6 mois sur 12 glissants), effet sur la rémunération (100 %, 50 %, 0 %), justificatif exigé, avis obligatoire, seuil de bascule, fractionnable, décompté du solde, réservé selon la situation |
| Barèmes | Événements familiaux : événement, nombre de jours, justificatif |
| Calendrier | Jours fériés, fermetures, définition de la semaine ouvrable |
| Chaînes de validation | Étapes par type et par niveau (supérieur, directeur, DRH, décideur), conditions, délégations, suppléance, escalade |
| Droits et soldes | Acquisition, report, expiration, fractionnement, prorata à l'entrée |
| Règles de service | Effectif minimal présent, chevauchements, périodes bloquées |
| Documents | Décision de congé, attestations de départ et de reprise, avec variables et signature scannée |
| Confidentialité | Le motif médical détaillé n'est visible que de la DRH ou du médecin, jamais de la hiérarchie |

### Circuit habituel d'une demande [modèle courant, à valider]
Plan annuel du service > demande de l'agent (type, dates, adresse, **intérimaire**) > **avis** du chef de service > **visa** du directeur > **contrôle des droits** par la DRH > **décision** du DG ou de la DRH par délégation > départ (attestation) > **reprise** (attestation) > mise à jour du dossier et du registre. Un congé peut être ajourné pour nécessité de service, avec motif écrit.

### Statuts d'une demande
Brouillon > soumise > avis du supérieur > visa > contrôle DRH > accordée ou refusée > en cours > terminée par la reprise de service ; ou annulée ou interrompue.

### Contrôles automatiques
Solde, week-ends et jours fériés exclus du décompte, chevauchements dans le service, effectif minimal, justificatifs, délais, intérimaire obligatoire, droits selon le régime et la situation.

### Garde-fous techniques [Décidé]
- Règles **datées** (date d'effet) : un changement de décret n'altère pas les congés déjà pris.
- Soldes par **écritures** (acquis, pris, ajusté, expiré) : toujours reconstituables, jamais écrasés.
- Aucune suppression : désactivation. Verrouillage d'un type utilisé par des demandes.
- Aperçu d'impact avant tout changement de règle ; tests de régression sur les règles de calcul.

### Liens avec e-Tache
Alerte quand un agent absent a des tâches échues ; proposition de réaffectation ; **intérim automatique** d'un responsable pendant son absence (grâce aux mandats).

### Usage et architecture
- Les agents déposent leurs demandes **dans la plateforme e-Tache** (pages responsives ; installation sur l'écran d'accueil possible) ; application mobile plus tard [Décidé].
- La logique métier vit dans des **services réutilisables** (soumettre, valider, refuser, annuler, calculer un solde) afin d'ajouter ensuite une **API versionnée** `/api/v1` (authentification par jeton) sans toucher aux règles.
- **Prérequis** : chaque agent a un compte lié à sa fiche (F10.1) et l'historique de carrière existe (F10.2).

### Sous-étapes
R0 réglages, calendrier, régimes ; R1 types d'absence et soldes ; R2 demande, validation, décisions, documents ; R3 calendrier d'équipe, règles de service, liens avec les tâches ; R4 congés médicaux, maternité, paternité, parental ; R5 états, exports, graphiques.

**Exports et graphiques** : absences par mois, soldes, congés non pris, taux d'absentéisme (agrégés par défaut).
**Besoin** : texte du décret (articles 77 à 107), formulaires et guide de procédure de la DRH, liste des types d'agents et de leur régime.
**Critères de recette** : une demande dépose, suit sa chaîne et produit la décision ; le solde est exact et reconstituable ; une règle modifiée ne change pas les congés passés ; le motif médical est invisible de la hiérarchie.

---

## F12. Graphiques et statistiques

**Bibliothèque** : **Chart.js** pour tout le neuf, avec migration progressive des graphiques ApexCharts actuels (dashboard, suivi de l'exécution, détail d'une tâche). Éviter de mélanger deux bibliothèques [À valider].

| Module | Diagrammes |
|---|---|
| Feuille de route | Avancement par axe, direction, action clé ; répartition des statuts ; **courbe d'évolution** (instantané quotidien) ; retards justifiés ou non ; charge par agent |
| Sous-tâches | Rythme de cochage, tâches sans mouvement, avancement réel contre prévu |
| Justifications | Motifs fréquents, délais de décision, reports par direction |
| Courrier et diligences | Arrivées et départs par mois, délai moyen, retards par direction |
| Personnel | Effectifs par direction, service, type d'agent ; pyramide des âges ; retraites à venir |
| RH | Absences par mois, soldes, taux d'absentéisme (agrégés) |

**Règles** : chaque graphique est **filtrable** (exercice, direction, période), **téléchargeable en image**, **inséré dans les exports PDF** et le rapport mensuel, respecte les **droits**, et a ses couleurs et seuils **réglables**.

---

## F13. Sécurité et mise en production

1. **Middlewares** sur les routes (aujourd'hui, les droits sont surtout contrôlés dans les contrôleurs et les vues).
2. **Planificateur** qui redémarre seul, avec contrôle de santé.
3. **Tests automatisés** : décideur selon le report, anti-doublon des alertes, situations temporelles, droits, règles RH.
4. `APP_DEBUG=false`, HTTPS, caches, emails en file d'attente.
5. **Sauvegardes** de la base **et du dossier des fichiers**, avec test de restauration.
6. Protection des fichiers privés et des courriers confidentiels.

---

## Annexe A : matrice des droits proposée [Par défaut, réglable]

| Fonction | ADMIN | DG | RD | RA / AGENT | DRH | COURRIER |
|---|---|---|---|---|---|---|
| Annuler une tâche | Oui | Non | Non | Non | Non | Non |
| Exporter les tâches | Tout | Tout | Son périmètre | Les siennes | Non | Non |
| Gérer ses sous-tâches | Oui | Oui | Lecture | **Ses** tâches | Non | Non |
| Modifier après l'échéance | Oui | Oui | Oui | Non | Non | Non |
| Clôturer / rouvrir un exercice | Oui | Clôture selon réglage | Non | Non | Non | Non |
| Reconduire | Oui | Oui | Sa direction (à confirmer) | Non | Non | Non |
| Enregistrer un courrier | Oui | Non | Non | Non | Non | Oui |
| Imputer un courrier | Oui | Oui | Réimputer | Non | Non | Non |
| Gérer le personnel | Oui | Lecture | Lecture (périmètre) | Sa fiche | Oui | Non |
| Valider un congé | Oui | Oui | Visa (périmètre) | Non | Contrôle | Non |
| Réglages et journal | Oui | Lecture | Non | Non | Réglages RH | Réglages courrier |

## Annexe B : réglages par module (récapitulatif)

| Module | Réglages principaux |
|---|---|
| Tâches | Qui annule, motifs d'annulation, impact sur les indicateurs, mode d'avancement par exercice |
| Sous-tâches | Qui crée, décoche, délai après échéance, nombre maximal, commentaire à la coche, statut automatique |
| Fichiers | Types, taille, nombre, visibilité, conservation, antivirus, quota |
| Exports | Colonnes, en-têtes, logo, mentions, seuils de volume |
| Alertes | Heures d'envoi, jours ouvrés, seuil de 15 jours des reports, motifs |
| Exercice | Contrôles de clôture (bloquants ou non), qui clôture et rouvre, reconduction |
| Courrier | Numérotation, types, urgences et délais, pré-tri, qui impute, instructions, confidentialité, conservation |
| Personnel | Mouvements, situations, types de pièces, droits par champ, seuils de rappel |
| RH | Régimes, types d'absence, barèmes, jours fériés, chaînes, soldes, règles de service, documents |
| IA | Activation, modèle, anonymisation, limite, volets, rôles, cache |
| Graphiques | Couleurs, seuils |

## Annexe C : dépendances entre fonctionnalités

- F4 (sous-tâches), F5 (fichiers) et F6 (journal) s'appuient sur les **fondations** : réglages, journal d'activité, pièces jointes génériques.
- F7 (clôture) vient **après F4 et F5** pour que le verrouillage couvre sous-tâches et fichiers.
- F9 (courrier) réutilise **F4, F5, F6** (une diligence est une tâche).
- F11 (RH) a besoin de **F10.1 et F10.2** (comptes, historique de carrière).
- F8 (synthèses) et F12 (courbes) ont besoin de l'**instantané quotidien** (0.6).
- La synthèse IA (F8) est **en dernier** et ne bloque aucune autre fonctionnalité.

## Annexe D : plus tard (hors périmètre actuel)

Application mobile, notifications push, signature électronique, pondération des sous-tâches, API ouverte, lecture automatique du courrier scanné (OCR).
