# e-Tâche : état des lieux fonctionnel et règles de gestion
Document établi le 19 octobre 2026, pour la recette et la notation du travail réalisé.

**Périmètre** : ce qui a été livré dans les échanges de développement (F2 à F7, Réglages, F9 étapes 1 et 2, organigramme et personnel), complété par les fichiers que l'utilisateur a lui-même modifiés (cascade de réimputation, associés, postes destinataires, dossier de l'agent) et par les décisions retenues depuis.
**Limite** : aucune fonctionnalité n'a été testée en conditions réelles par l'assistant (seule la syntaxe PHP, Blade et JavaScript a été contrôlée). Les lots dont je n'ai pas vu le code (F9 étapes 3 et 4, F4b, F8, F12, page « Mes tâches ») sont signalés **[à confirmer]**.

---

## 1. Vue d'ensemble

| Domaine | Contenu | État |
|---|---|---|
| Fondations | Réglages, journal d'activité, pièces jointes génériques, verrou d'exercice | Livré, à recetter |
| F2 | Exports Excel et PDF des tâches | Livré, en recette |
| F3 | Gantt par axe | Livré, en recette |
| F4 | Sous-tâches et avancement calculé | Livré, en recette |
| F5 | Fichiers livrables | Livré, en recette |
| F6 | Journal d'activité | Livré, à recetter |
| F7 | Clôture, verrouillage, reconduction d'exercice | Livré, à recetter |
| F9 étape 1 | Registre du courrier arrivée | Livré |
| F9 étape 2 | Imputation, diligences, fiche de transmission | Livré, étendu par l'utilisateur (cascade, associés) |
| F9 étapes 3 et 4 | Traitement, réponse, départ, archivage, alertes, exports | Décidé, **[à confirmer]** |
| Organigramme et personnel | Fonctions, affectation et intérim, fiche agent, carrière, dossier | Livré |
| F4b | Instruction du DG : sous-tâches validées par le supérieur | Décidé, **[à confirmer]** |
| F8, F12 | Synthèses et graphiques | Décidé, **[à confirmer]** |
| F1 | Annulation d'une tâche | **Abandonné** (existe déjà dans l'application) |
| F11, F13, 0.6 | RH, sécurité, instantané quotidien | Reportés après la recette |

---

## 2. Profils et droits

| Profil | Rôle dans l'application |
|---|---|
| ADMIN | Tous les droits : paramétrage, réglages, affectations, dossier des agents, comptes |
| DG | Direction générale : impute les courriers, clôture un exercice (réglable), consulte journal et dossiers d'agents |
| RD | Responsable de direction (selon le mandat en cours) ; sans mandat, il agit comme un RA |
| RA | Responsable de tâche |
| COURRIER | Service courrier et secrétariat du DG : enregistre les courriers |
| Agent | Tout utilisateur lié à une fiche agent : « Mes tâches », « Mes courriers », sa propre fiche |

**Droits courrier**

| Action | Qui (réglable dans Réglages > Courrier) |
|---|---|
| Consulter le registre | ADMIN, DG, COURRIER |
| Enregistrer ou modifier un courrier | ADMIN, COURRIER ; modification possible tant que le courrier n'est pas imputé |
| Imputer | ADMIN, DG |
| Réimputer | ADMIN, ou le responsable d'une diligence qui est titulaire d'un poste configuré « peut réimputer » |
| Annuler une diligence | Profils qui imputent, avec motif |
| Paramétrer le courrier | ADMIN |

---

## 3. Fondations

### 3.1 Écran Réglages
Cinq modules à l'origine (Exercice, Sous-tâches, Fichiers, Exports, Journal), complétés par **Courrier** et **Dossier du personnel**.
- Seules les valeurs modifiées sont stockées ; toute valeur peut être rétablie par défaut.
- Un réglage peut être **verrouillé** par la situation (exemple : le format de numérotation ne change plus dès qu'un numéro a été émis).
- Chaque changement de réglage est tracé au journal.

### 3.2 Journal d'activité (F6)
- Table **non modifiable et non supprimable** (toute tentative lève une exception).
- Événements tracés : sous-tâches, fichiers, exports, réglages, exercice (clôture, réouverture, reconduction), courrier (enregistrement, modification, scan, imputation, prise en charge, solde, annulation, consultation, avis), personnel (affectation, fin d'affectation, modification de carrière, dossier, consultation du dossier).
- Page journal réservée à l'ADMIN et au DG, avec filtres et export Excel (5 000 lignes au plus, réglable).
- La consultation d'un courrier confidentiel et d'un document du dossier d'un agent est toujours tracée.

### 3.3 Pièces jointes génériques
Stockage privé (hors dossier public), type de contenu réellement vérifié, versions conservées, suppression logique, accès par le serveur selon les droits.

---

## 4. Feuille de route

### 4.1 F2 Exports
- 9 types : toutes, en retard, non justifiées, justifiées ou en attente, en cours, terminées, à venir (J+N), annulées, sans responsable.
- Les **filtres de l'écran** s'appliquent ; les droits sont respectés ; le nom du fichier est horodaté ; chaque export est tracé.
- PDF en paysage A4, mention « Document à usage interne » réglable. Délais « à venir » réglables (7, 15 et 30 jours par défaut).

### 4.2 F3 Gantt par axe
Trois niveaux emboîtés (axes, actions clés, tâches) avec sélecteur de niveau. Chaque axe et action clé porte une barre de synthèse : avancement moyen, pastilles de retards et de retards non justifiés. Export PDF par impression.

### 4.3 F4 Sous-tâches et avancement
| Règle | Détail |
|---|---|
| Mode d'avancement | Choisi **par exercice** : Manuel, ou Par sous-tâches |
| Calcul | Avancement = sous-tâches cochées ÷ total × 100 ; le champ est en lecture seule dans ce mode |
| Statut | Mis à jour automatiquement (réglable) |
| Limites | 50 sous-tâches par tâche (réglable) |
| Droits | Gestion par ADMIN et DG ; après l'échéance, ADMIN, DG et RD |
| Affichage | Indicateur « 3/5 » dans toutes les vues |
| Protection | Verrouillé si l'exercice est clos |

### 4.4 F5 Fichiers livrables
Dépôt sur une tâche ou une sous-tâche ; taille maximale 10 Mo, 10 fichiers par élément, extensions réglables ; versions conservées ; téléchargement groupé (zip) ; dépôt par ADMIN et DG, suppression par ADMIN, DG et RD (réglable) ; verrouillé si l'exercice est clos ou la tâche annulée.

### 4.5 F7 Clôture, verrouillage, reconduction
**Clôture**
- ADMIN et DG clôturent (réglable) ; seul l'ADMIN rouvre, avec un motif.
- **Cinq contrôles** de niveau bloquant, avertissement ou ignoré ; par défaut, seules les justifications en attente bloquent.
- Confirmation par saisie de l'année. L'**archive Excel est générée avant** la clôture : si elle échoue, l'exercice n'est pas clos.
- Les tâches pluriannuelles sont signalées (« ce ne sont pas des retards »). Alertes et mise à jour des situations cessent pour un exercice clos.
- **Verrou** sur tâches, sous-tâches, fichiers, axes, actions clés, actions mesurables et justifications (erreur 423 sinon).

**Reconduction**
- Jamais automatique. Source : n'importe quel exercice ; destination : un exercice ouvert et différent.
- Sont reconduites les tâches non terminées et non annulées, pas déjà reconduites vers cette destination. Échéance obligatoire, préremplie si valide.
- Destination choisie **par tâche** ; un volet « structure seule » reprend un arbre axes et actions à cocher, sans doublon.
- Affectations et sous-tâches non cochées sont reprises en option. Jamais repris : fichiers, justifications, historiques, sous-tâches cochées, dépendances.
- Tout ou rien (transaction) ; la tâche source est marquée « reconduite vers N+1 ».

### 4.6 Page « Mes tâches » **[règle retenue, à confirmer]**
Une tâche marquée terminée par l'agent ne peut plus être modifiée par lui ; seul l'administrateur le peut. L'onglet des tâches du jour utilise une chronologie avec une fenêtre de mise à jour (priorité, statut d'exécution, avancement).

---

## 5. Courrier (F9)

### 5.1 Enregistrement
| Règle | Détail |
|---|---|
| Numérotation | `A26-0001` (arrivée), `D26-0001` (départ), `DL26-0001` (diligence) ; remise à zéro chaque année ; numéro attribué sans doublon ni trou ; **format verrouillé** après le premier numéro |
| Origine | Service courrier ou secrétariat du DG |
| Dates | Réception : pas dans le futur ; date du courrier : pas après la réception |
| Champs | Référence, expéditeur, objet, type, urgence, mode de transmission (avec précision si « Autre »), nombre de pièces jointes, observations |
| Scan | Glisser-déposer avec aperçu avant enregistrement ; PDF, JPG ou PNG ; 20 Mo ; contenu vérifié ; obligatoire (réglable) ; versions conservées |
| Doublon | Même expéditeur, même référence et même date du courrier : avertissement, enregistrement possible après confirmation |
| Confidentialité | Visible de l'ADMIN, du DG, de la personne qui l'a enregistré et des personnes désignées par l'imputation (responsables et associés) ; fiche et scan tracés à chaque consultation |
| Statuts | À imputer, Imputé, En traitement, **Traité**, **Classé** (vocabulaire du registre papier) |
| Mention « B » du registre papier | Abandonnée |

La fiche du courrier affiche le document dans une visionneuse (plein écran), la fiche, l'imputation et le parcours.

### 5.2 Paramétrage (ADMIN)
- **Types de courrier** (lettre, note, demande, invitation, rapport, autre) et **urgences** (normal, urgent, très urgent) avec couleur et délai proposé.
- **12 instructions du DG** de la fiche papier : pour instruction, me représenter, allocution à préparer, participation ou invitation, m'en parler, courrier réponse à rédiger, nécessaire à faire, me voir, accord, pour information, pour suivi, archiver.
- **Délais de traitement** : Urgent (1 jour), 24 h (1 jour), 48 h (2 jours), Autre (date saisie).
- **Postes destinataires** : sélection de postes de l'organigramme (DG, DGA, directeur par direction, sous-directeur, chef de service). Le titulaire ou l'intérimaire est affiché dès la sélection ; une seule configuration par poste ; indication « peut réimputer » ; suppression refusée si des diligences y sont rattachées.
- Chaque bloc de la page se plie et se déplie au clic sur son en-tête.

### 5.3 Imputation
- Faite par le DG (et l'ADMIN) depuis la fiche du courrier, dans un formulaire qui reprend la fiche de transmission.
- **Destinataires** : un poste de l'organigramme configuré, ou un agent directement.
- **Rôles** : travail indépendant (responsable principal), avis ou aide associé, copie ou information.
- Instructions à cocher, délai, date d'imputation (pas avant la réception, pas dans le futur), échéance calculée ou saisie, instructions particulières, observations.
- Imputation directe à un agent : son supérieur hiérarchique est **informé** (réglable).
- Un courrier « Archiver » ou « Pour information » est classé directement, sans diligence **[règle retenue]**.

### 5.4 Diligences
| Règle | Détail |
|---|---|
| Naissance | Une diligence par travail principal, numérotée `DL26-xxxx`, avec responsable, échéance, instructions |
| Statuts | À prendre en charge, En cours, **En retard** (calculé : échéance dépassée), Réimputée, Traitée, Annulée |
| Prise en charge | Par le responsable, l'ADMIN ou le DG : c'est l'accusé de prise en charge |
| Solde | Par le responsable, l'ADMIN ou le DG ; commentaire facultatif |
| Annulation | Par les profils qui imputent ; motif obligatoire (5 caractères au moins) |
| Courrier | Passe à « Traité » quand plus aucune diligence n'est ouverte et qu'une au moins est soldée |
| Responsable | Résolu **à l'imputation** : l'agent lui-même, ou le titulaire (ou l'intérimaire) du poste. Si personne n'occupe le poste, la diligence est créée sans responsable et un message le signale |
| Notification | Cloche pour le responsable ; aucune pour les associés (à corriger) |

**Écrans** : tableau des diligences (ADMIN, DG, COURRIER : indicateurs, filtres par statut et recherche), « Mes courriers à traiter » (cartes avec échéance, copies reçues, avis demandés), page d'une diligence (consignes, avancement, chaîne de réimputation, avis).

### 5.5 Cascade de réimputation et associés (ajoutés par l'utilisateur)
- Une réimputation crée une **imputation fille** et des diligences filles reliées à la diligence parente, qui passe à « Réimputée » ; la chaîne s'affiche des deux côtés.
- **Avis ou aide** : rattaché à une diligence principale ; le destinataire dépose des commentaires tant que la diligence est ouverte ; l'avis est tracé.
- **Copie** : lecture seule ; le courrier apparaît dans « Courriers reçus en copie ».
- Les associés voient le courrier, même confidentiel.

### 5.6 Fiche de transmission PDF
Reprend le modèle papier de la DGEPN : en-tête officiel (réglable), informations du courrier, mode de transmission, imputation avec cases cochées (associés compris), instructions et délais cochés, instructions particulières, observations.

### 5.7 Décisions retenues pour la suite du courrier
- **Réponse** : le visa **monte toute la chaîne hiérarchique**.
- Le **courrier départ** `D26-` est créé automatiquement après une réponse signée, **réglable** dans les Paramètres ; un numéro par destinataire ; n° d'archives saisi à la main.
- Imputation d'un **chargé d'études** : on choisit l'agent précis ; le **service courrier** et les services rattachés à la DG ont un responsable et s'imputent comme des services.
- Archivage : consultable, « à conserver jusqu'au », aucune suppression automatique.
- Alertes d'échéance et de retard des diligences : **deux déclenchements**, le planificateur et la visite d'un utilisateur.

---

## 6. Organigramme et personnel

### 6.1 Fonctions et postes
| Règle | Détail |
|---|---|
| Fonctions | Directeur Général, DGA, Directeur, Sous-directeur, Chef de service, Chargé d'études, Conseiller, Agent, Technicien de surface (liste ouverte, gérée par l'ADMIN) |
| Postes à titulaire unique | DG, DGA, directeur, sous-directeur, chef de service : tenus par des **mandats** (titulaire ou intérim), historique conservé |
| Autres fonctions | Rattachement à un **service**, ou à la **Direction Générale** (chargé d'études, conseiller) |
| Champ `fonction` de l'agent | Contient la valeur du poste : « Directeur Général », « Directeur - Direction X », « Chef de service - Service Y », « Agent »... |
| Fonctions d'origine | Ni supprimables, ni changeables de catégorie |

### 6.2 Affectation à un poste (fenêtre de la liste du personnel et de la fiche, ADMIN)
- La fonction choisie détermine la liste de postes : directions, sous-directions ou services.
- Un poste à titulaire unique **déjà occupé** est signalé avec l'occupant ; l'affectation n'a lieu qu'**après confirmation** : le nouveau devient titulaire, l'ancien est remplacé, l'historique est conservé.
- **Intérim** : case dédiée ; un intérimaire ne change pas la fonction habituelle de l'agent ; un nouveau titulaire met fin à l'intérim en cours.
- **Un agent n'est titulaire que d'un poste** : en accepter un nouveau (ou une fonction sans titulaire unique) met fin à son poste actuel après confirmation. Un titulaire remplacé perd sa fonction.
- **Fin d'affectation** (bouton « Mettre fin » de la fenêtre ou lien « mettre fin » de la fiche) : la date saisie est le **dernier jour au poste, inclus** : l'agent reste en poste jusqu'à minuit ; motif obligatoire. Pour une fonction de titulaire, le poste devient vacant et la fonction de l'agent est vidée.
- Tout est tracé (affectation, fin d'affectation).

### 6.3 Pages
- **Responsables / organigramme** : **consultation seule** ; un clic sur une unité ouvre l'historique du poste.
- **Liste du personnel** : colonnes Fonction (avec postes occupés), Service, Compte (oui ou non).
- **Comptes** : la liste de création ne propose que les agents **sans compte** ; l'agent choisi préremplit le nom et l'email (modifiables). La recherche dans la liste d'agents est possible.

### 6.4 Fiche agent
Le bandeau (photo, nom, poste, indicateurs) est inchangé ; dessous, trois colonnes de cartes compactes :

| Carte | Contenu |
|---|---|
| Identité et état civil | Civilité, genre, naissance, statut matrimonial, enfants, père, mère |
| Contact | Email, deux téléphones, habitation |
| Affectation | Poste, direction, sous-direction, service, supérieur, responsable de |
| Carrière | Emploi, grade, classe, échelon, **indice**, matricule, type d'agent, première prise de service, prise de service, **ancienneté** calculée, départ à la retraite avec le temps restant |
| Dossier | Documents de l'agent |
| Mouvements de poste | Postes à titulaire unique (période, état, motif de fin) et historique des affectations |

**Droits d'édition**

| Qui | Peut modifier |
|---|---|
| ADMIN | Tout, y compris la carrière et le dossier |
| Agent sur sa propre fiche | Photo, identité et état civil, coordonnées ; **pas** matricule, type d'agent, emploi, statut, fonction ni service |

### 6.5 Carrière
- Grade, classe et échelon vont **ensemble** : la combinaison doit exister dans l'échelle de traitement (12 grades de A7 à D1, 4 classes, échelons 1 à 4, 153 indices). L'indice s'affiche automatiquement ; une combinaison inexistante est refusée. Ces tables sont en base, sans écran de gestion.
- Dates : la prise de service ne précède pas la première prise de service ; la retraite et la première prise de service sont postérieures à la naissance ; le décès n'est pas antérieur à la naissance.
- **La date de décès et la date de départ à la retraite sont informatives** : elles ne rendent pas l'agent inactif et ne libèrent pas son poste.

### 6.6 Dossier de l'agent
- Types : CV, carte nationale d'identité, diplôme, acte de naissance, attestation, autre.
- **Consultation** : ADMIN, DG et l'agent lui-même ; chaque consultation est tracée.
- **Dépôt** : ADMIN et agent concerné ; **suppression** : ADMIN seul (suppression logique).
- Fichiers PDF, images, Word ; taille et extensions réglables (10 Mo par défaut) ; contenu vérifié.
- **Règle actuelle** : un type de document ne se dépose **qu'une fois**, même si le premier a été supprimé, et la liste du formulaire n'affiche que les types encore disponibles. Cette règle s'applique aussi à l'ADMIN (voir 8).

---

## 7. Décisions retenues pour la suite

| Sujet | Décision |
|---|---|
| Ordre | Socle du lot « diligences et suivi des postes », puis F9 étapes 3 et 4 et F4b en parallèle ; arrêt après F8 et F12, **recette complète** (tables vidées pour repartir à neuf), notation du travail, puis les lots restants |
| F4b | La sous-tâche est **validée par le supérieur hiérarchique direct** du responsable (via les postes) ; on ne désigne plus d'agent par sous-tâche ; validation par le supérieur, puis coche par le responsable |
| F1 | Abandonné |
| Instantané quotidien (0.6) | Non prioritaire |
| F10 | Gardé seulement ce qui sert F11 ou la qualité des données : fiches incomplètes avec export, doublons, journal des modifications du formulaire agent, archivage au lieu de suppression, comptes en masse (profil AGENT, désactivation au départ), import Excel (l'utilisateur n'a pas de fichier : **proposer un modèle et des écrans**), organigramme interactif avec postes vacants (appuyé sur la page Responsables), tableau de bord du personnel, dossier avec validité, rappels, compétences et formations, charge de travail et sur-affectation sur la fiche |
| IA (F8) | La clé API est payée : la synthèse IA est de nouveau possible, avec agrégats seulement |
| Serveur | Des commandes peuvent être lancées ; alertes par planificateur **et** à la visite ; hébergement en ligne payé dont on ne connaît pas le type |
| Design | En-têtes des pages courrier plus doux : bords colorés, pastille, fond avec motif et dégradé |

---

## 8. Points de vigilance connus
1. **Responsable d'une diligence figé** à l'imputation : il ne suit pas le changement de titulaire d'un poste. Principe voulu : un agent est suivi partout ; un poste ou une unité va au titulaire ou à l'intérimaire du jour.
2. **Plusieurs travaux indépendants** dans une imputation créent plusieurs diligences : modèle « un chef de file par imputation » proposé, en attente de décision.
3. **Réimputation** : réservée aux titulaires de postes configurés, liste de destinataires non limitée à la hiérarchie, pas d'échéance maximale ni de renvoi motivé, parent non refermé automatiquement.
4. **Associés non notifiés** (avis, copie).
5. Directions, sous-directions et services ne sont plus imputables que **via un poste configuré** ; les anciens postes sans niveau (service courrier, chargé d'études) n'apparaissent plus.
6. **Dossier de l'agent** : un diplôme, une attestation ou un « autre document » une seule fois ; un document supprimé ne peut pas être redéposé.
7. Séparateurs différents entre le champ `fonction` (« - ») et les listes de postes (« — »).
8. **Contrôle d'accès côté serveur** des écrans Paramètres, Utilisateurs, Nominations et Fonctions encore limité au menu (F13).
9. Points anciens : import de matrice refusant un libellé d'axe déjà présent dans un autre exercice ; marquage de reconduction non levé à la suppression d'une tâche reconduite ; pas d'unicité de l'exercice actif.
10. **Recette** : tout reste à tester en conditions réelles, en particulier F6, F7, Réglages, le courrier et les affectations.

---

## 9. Annexe : migrations livrées
| N° | Objet |
|---|---|
| 28 à 31 | Mode d'avancement, sous-tâches, pièces jointes, journal d'activité |
| 32 à 35 | Clôture, reconduction, unicité des libellés d'axe par exercice, réglages |
| 36 à 40 | Types et urgences de courrier, courriers et compteurs, profil COURRIER, champs de la fiche de transmission |
| 41, 42 | Instructions, délais, postes destinataires, imputations, diligences |
| 43 | Fonctions et niveau de mandat en texte |
| 44, 45 | Listes du personnel, échelle de traitement, champs de carrière |
| 46 | Associés, avis, positions de l'organigramme des postes destinataires, parenté des diligences (ajoutée par l'utilisateur) |
