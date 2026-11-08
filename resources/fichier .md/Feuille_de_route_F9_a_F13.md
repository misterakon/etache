# e-Tâche : feuille de route de F9 à F13 (et fonctions associées)
Mise à jour : 10 octobre 2026. Référence fonctionnelle : `etache_nouvelles_fonctionnalites.md` (cahier des charges). Travail en cours détaillé : `Reste_a_faire_etache.md`.
Légende : **Fait**, **Partiel**, **À faire**.

## Vue d'ensemble
| Fonction | Sujet | État | Dépend de |
|---|---|---|---|
| 0.6 | Instantané quotidien de l'avancement | À faire (à lancer tôt) | : |
| F1 | Annulation d'une tâche (ADMIN) | À faire (mise de côté) | F7 pour l'exercice clos |
| F4b | Instruction du DG : sous-tâches par responsable, validées par le supérieur | À faire | F4, F10 (postes) |
| **F9** | **Courrier et diligences** | **Étapes 1 et 2 faites, cascade et associés ajoutés par l'utilisateur ; étapes 3 et 4 à faire** | F4, F5, F6 |
| F8 | Synthèses et récapitulatifs | À faire | 0.6 |
| **F10** | **Personnel** | **Partiel** (voir 3) | F6 |
| F11 | RH : congés et absences | À faire | F10.1, F10.2 |
| F12 | Graphiques (Chart.js) | À faire | 0.6 |
| F13 | Sécurité et mise en production | À faire (en partie à avancer) | : |

## Ordre proposé
1. **Terminer le lot diligences et suivi des postes** (`Reste_a_faire_etache.md`, 3.1).
2. **F9 étape 3** (réimputation complète, traitement, réponse) puis **étape 4** (départ, archivage, alertes, exports).
3. **0.6 instantané quotidien** : léger, et l'historique ne se constitue qu'à partir de son lancement. À insérer dès que F9 étape 2 est stabilisée.
4. **F4b instruction du DG** (sous-tâches validées).
5. **F10.1** (fiabilisation) puis **F10.2** (historique, organigramme, tableau de bord) : indispensables à F11.
6. **F1** (petite), puis **F8 sans IA**, **F12**, enfin **F11** et la synthèse IA.
7. **F13** : les middlewares et la protection des fichiers sensibles dès que possible ; sauvegardes, tests et mise en production en fin de parcours.

---

## 1. F9 Courrier et diligences : ce qui reste
**Fait** : enregistrement (scan glisser-déposer, visionneuse, doublons, confidentialité), numérotation, types, urgences, instructions et délais, imputation par le DG, diligences, prise en charge, solder, annuler, tableau, « Mes courriers », fiche de transmission PDF, notifications ; cascade et associés (avis, copie) ajoutés par l'utilisateur.

**Étape 3**
- **Réimputation complète** : droit par défaut selon la fonction, liste limitée à la hiérarchie, échéance bornée par la parente, **renvoi motivé** vers le niveau supérieur, historique, notifications (associés compris), clôture en cascade.
- **Traitement** d'une diligence : sous-tâches, fichiers, **demande de délai** (circuit de report existant), réutilisation de F4 et F5.
- **Réponse** : projet joint, visas, signature scannée déposée et tracée ; plusieurs réponses possibles.
- **Pré-tri** par le cabinet : optionnel, désactivé par défaut.
- **Alertes** d'échéance et de retard des diligences (comme pour les tâches).

**Étape 4**
- **Registre départ** `D26-0001` : un numéro **par destinataire** (comme le registre papier), objet saisi une fois, lien avec le courrier d'origine, nombre de pièces, **n° d'archives saisi à la main**.
- **Archivage** consultable, « à conserver jusqu'au », aucune suppression automatique ; statut « Classé ».
- **Exports** : registre départ, diligences par statut, direction et période (demande du DG) ; le registre arrivée s'exporte déjà.
- **Graphiques** (F12) et **compteurs agrégés** pour l'IA (F8) : jamais l'objet des courriers.
- **Documents** : fiche imprimable du registre papier, accusé de prise en charge.

**Décisions ouvertes** : chaîne de visas de la réponse, création automatique du départ, types de dossier, et les points du lot diligences.

---

## 2. F4b Instruction du Directeur Général : décomposition des tâches
> Chaque responsable décompose une tâche en sous-tâches **au bénéfice de l'agent concerné**, précise pour chacune les **modalités et conditions d'exécution**, et toute sous-tâche est **validée par le supérieur hiérarchique compétent** (conformité et traçabilité).
- **Existe** (F4) : sous-tâches cochables, avancement calculé, fichiers, journal.
- **À faire** : agent destinataire par sous-tâche ; modalités et conditions ; circuit « à valider, validée, refusée avec motif » ; traçabilité complète ; périmètre de « chaque responsable ».
- **Questions** : qui est le supérieur compétent (les postes et mandats permettent de le déterminer) ? les sous-tâches non validées comptent-elles dans l'avancement (proposition : non) ? l'agent la voit-il avant validation et peut-il la contester ? champs libres ou rubriques ? notifications et exports. Voir `A_reprendre_apres_F9.md`.
- Une diligence étant traitée par sous-tâches, ce circuit s'appliquera aussi au courrier.

## 3. F10 Gestion du personnel
### F10.1 Fiabilisation des fiches
| Point du cahier | État |
|---|---|
| 1. Contrôles de qualité (matricule et email uniques, format de téléphone, âge plausible, service obligatoire) | Partiel : matricule unique existe ; reste email, téléphone, âge, service (et la règle « service obligatoire » est à revoir avec les fonctions) |
| 2. Tableau « fiches incomplètes » avec export | À faire |
| 3. Détection de doublons à la création | À faire |
| 4. Import Excel du personnel (modèle, prévisualisation, tout ou rien) | À faire (besoin : fichier Excel) |
| 5. Journal des modifications de fiche | Partiel : affectations, carrière et dossier sont journalisés ; pas les modifications du formulaire agent |
| 6. Archivage d'un agent au lieu de la suppression | À faire (`actif` existe, la suppression aussi) |
| 7. Comptes en masse, profil AGENT par défaut, mot de passe provisoire, désactivation au départ | Partiel : colonne « Compte », création d'un compte avec nom et email préremplis, agents sans compte ; reste la création en masse, le profil AGENT et la désactivation automatique |

### F10.2 Historique et organisation
| Point du cahier | État |
|---|---|
| 1. Mouvements datés (affectation, mutation, promotion, changement d'emploi) et frise de carrière | Partiel : affectations et intérims avec dates, acte et motif, carte « Mouvements de poste » ; reste mutation, promotion, changement d'emploi et la frise |
| 2. Situations administratives (activité, détachement, disponibilité, congé de longue durée, retraite) | À faire (les dates de retraite et de décès sont seulement informatives) |
| 3. Anciens mandats et tâches rattachés au bon service à la bonne date | Partiel pour les mandats ; à faire pour les tâches |
| 4. Organigramme interactif avec postes vacants | À faire (la page Responsables est en consultation seule) |
| 5. Tableau de bord du personnel (effectifs, pyramide des âges, retraites prévues) | À faire |
| 6. Dossier de l'agent (type, date, validité, droits stricts, rappels d'échéance, compétences et formations, contacts d'urgence) | Partiel : dépôt, types, droits (ADMIN, DG, agent), consultations tracées ; reste date, validité, rappels, compétences, formations, contacts |
| 7. Liens avec e-Tâche (charge de travail sur la fiche, alerte de sur-affectation, réaffectation au départ) | À faire |

### Ajoutés en dehors du cahier (faits)
Fonctions et postes de l'organigramme, affectation avec intérim, champs de carrière (grade, classe, échelon, indice, dates de service), état civil, fiche agent v3. À reporter dans le cahier : confidentialité **par champ** (téléphone personnel, date de naissance) et accès aux dossiers sensibles tracé.

**Besoins** : fichier Excel du personnel, liste des agents sans compte.
**Critères de recette** : fiche incomplète visible dans un tableau dédié ; doublon signalé à la saisie ; un mouvement conserve l'ancien service ; aucun agent supprimé, seulement archivé.

## 4. F11 Module RH : congés et absences (à faire)
- **Prérequis** : F10.1 (compte lié à chaque agent, profils AGENT et DRH) et F10.2 (historique de carrière).
- **Base légale** : loi n° 2023-892 du 23 novembre 2023, décret n° 2025-120 du 26 février 2025 (articles 77 à 107). **À valider avec la DRH** : barèmes des événements familiaux, report et fractionnement, délais, signataires, régime des contractuels.
- **Principe** : les règles sont des **données datées** (source citée), soldes par **écritures** reconstituables, aucune suppression.
- **Sous-étapes** : R0 réglages, calendrier, régimes ; R1 types d'absence et soldes ; R2 demande, validation, décisions, documents ; R3 calendrier d'équipe, règles de service, liens avec les tâches ; R4 congés médicaux, maternité, paternité, parental ; R5 états, exports, graphiques.
- **Liens e-Tâche** : alerte quand un absent a des tâches échues, proposition de réaffectation, **intérim automatique** d'un responsable absent (s'appuie sur les mandats, déjà en place).
- **Besoins** : texte du décret, formulaires et guide de la DRH, types d'agents et régime de chacun.
- **Architecture** : services métier réutilisables, puis API `/api/v1` ; confidentialité du motif médical (invisible de la hiérarchie).

## 5. F12 Graphiques et statistiques (à faire)
- **Chart.js** pour le neuf, migration progressive des graphiques ApexCharts (dashboard, suivi de l'exécution, détail d'une tâche) ; ne pas mélanger deux bibliothèques [à valider].
- **Modules** : feuille de route (axes, directions, statuts, **courbe d'évolution** par l'instantané), sous-tâches, justifications, **courrier et diligences** (arrivées et départs par mois, délai moyen, retards par direction), personnel (effectifs, pyramide des âges, retraites), RH (agrégés).
- **Règles** : filtrables, téléchargeables en image, insérés dans les exports PDF et le rapport mensuel, respect des droits, couleurs et seuils réglables.

## 6. F13 Sécurité et mise en production (à faire)
1. **Middlewares** sur les routes. Aujourd'hui les droits sont dans les contrôleurs et les vues ; **écrans Paramètres, Utilisateurs, Nominations et Fonctions** à protéger côté serveur.
2. Planificateur qui redémarre seul, avec contrôle de santé (il portera aussi l'instantané quotidien, les alertes de diligences et le suivi des postes).
3. Tests automatisés : décideur selon le report, anti-doublon des alertes, situations temporelles, droits, règles RH.
4. `APP_DEBUG=false`, HTTPS, caches, emails en file d'attente.
5. Sauvegardes de la base **et du dossier des fichiers**, avec test de restauration.
6. Protection des fichiers privés et des courriers confidentiels (scans, dossiers d'agents).

## 7. Autres éléments
- **0.6 Instantané quotidien** : chaque nuit, état par tâche, direction et axe (nombre par statut, avancement moyen, retards, retards non justifiés). À lancer tôt ; nécessaire à F8 et F12.
- **F8 Synthèses** : chiffres et graphiques, récapitulatif automatique par modèles de phrases, puis synthèse IA en dernier (agrégats anonymisés seulement ; activation reportée faute de compte et de crédits API).
- **F1 Annulation d'une tâche** : ADMIN, motif obligatoire, sortie des retards et alertes, réactivation possible, figée dans un exercice clos.
- **F7 et F6** : recette en conditions réelles encore à faire (clôture, reconduction, journal, Réglages). Points ouverts : import de matrice et libellé d'axe unique par exercice, marquage de reconduction non levé à la suppression, unicité de l'exercice actif, `SHOW CREATE TABLE axes`.
- **Plus tard (hors périmètre)** : application mobile, notifications push, signature électronique, pondération des sous-tâches, API ouverte, OCR du courrier scanné.
