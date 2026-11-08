# e-Tâche Laravel : fiche de reprise (état au 15/10/2026)

À coller au début de la nouvelle conversation, avec les fichiers du projet à jour.
Projet : application Laravel (Côte d'Ivoire, DGEPN), Laravel 13 / PHP 8.3, thème CUBA (menu horizontal), développement en local sous WAMP (`127.0.0.1/etache`). Production : hébergement en ligne, peu de contrôle sur le serveur.

---

## 1. Règles de travail à respecter (préférences de l'utilisateur)
- Répondre en français, **concis**. Ne pas réécrire tout un fichier si quelques lignes changent : donner des **insertions ciblées**, sauf demande contraire.
- **Toujours partir des fichiers actuels de l'utilisateur** (les copies du projet sont parfois périmées : une livraison a écrasé son menu à cause de cela). Demander les versions actuelles avant de livrer un fichier qu'il a pu retoucher.
- Livrer en **zip + LISEZ-MOI.md** (installation, routes à ajouter à la main, migrations, réglages). Ne jamais donner `routes/web.php` ou `Reglages.php` entiers quand une insertion suffit.
- Contrôles avant livraison : `php -l`, compilation Blade, tests par modèles simulés. **Rien n'est jamais exécuté dans l'application réelle** : le dire honnêtement.
- Design : doux, clair. L'utilisateur a mis son en-tête `cr-hero` en bleu foncé (`--h: #033074` dans son `courrier.css`). Menu **horizontal** obligatoire.
- Le thème force certains `<span>` en ligne : écrire les composants en `<div>` avec `display:… !important`.
- L'utilisateur a abandonné la refonte de la page du personnel (« laisse tomber ») : ne pas la reprendre.

## 2. Décisions déjà prises (ne pas redemander)
- Pas d'annulation de tâche supplémentaire (existe déjà). Synthèse IA : clé API dans `.env` (`ANTHROPIC_API_KEY`), jamais de noms/objets/titres envoyés, seulement des totaux.
- Instantané quotidien : « à la première visite » + commande planifiée.
- Courrier : réponse = circuit de visa (option `courrier.reponse_visa_chaine` : toute la chaîne chef de service → sous-directeur → directeur → DG, ou DG seul) ; création du départ réglable (`courrier.depart_auto`).
- Libellés : « Traitée » (diligence), « Traité » (courrier), « Classé » (archive). « Archiver »/« Pour information » seuls = courrier classé sans diligence.
- **Plus d'agent désigné** sur les sous-tâches ni sur les actions du plan de traitement (« l'important est de voir ce qui est réalisé »). Validateur des actions et sous-tâches = chaîne complète (`ChaineHierarchique` : chef de service, sous-directeur, directeur, DG).
- Renvoi : « Renvoyer au supérieur » (diligence réimputée) ou « Renvoyer au DG/à l'imputeur » (diligence reçue directement : le courrier redevient « À imputer »).
- Annulation d'une réponse : brouillon / à corriger = immédiate ; en circuit sans visa = immédiate (motif) ; avec au moins un visa = demande soumise à l'accord du **dernier visa** (circuit suspendu, ADMIN/DG peuvent trancher, refus avec motif).
- Validation de la clôture d'une tâche par le supérieur : **réglage, désactivé par défaut** ; tâche reste « En cours » (99 % max) jusqu'à validation ; retard calculé à la date de soumission.
- Confidentialité : le contenu d'un courrier confidentiel n'est visible que des personnes désignées (chaîne d'imputation + avis/copie + circuit de visa), de l'ADMIN, du DG, de l'enregistreur ; le supérieur hiérarchique non désigné voit le suivi mais pas le contenu.
- Non retenus (F10) : situations administratives, promotions/mutations, contacts d'urgence.

---

## 3. Livraisons déjà faites (dans l'ordre) et état d'installation
Les zips sont dans `/mnt/user-data/outputs/` de la conversation précédente ; à redemander si besoin.

| # | Livraison | Contenu principal | Migration / action manuelle |
|---|---|---|---|
| 1 | `etache_maj`, `etache_f9_complet`, `etache_f10_complet` (sessions précédentes) | Courrier F9 étapes 1-4, personnel/RH F10, sécurité routes F13 partie 1, F4b sous-tâches | migrations 47 à 52 |
| 2 | `entete_courrier_2026-10-11` | En-tête de page clair avec bord coloré, motif, pastilles ; couleurs par module (`cr-hero--vert/violet/ambre/ardoise/rose/bleu`) | CSS `courrier.css` (l'utilisateur l'a retouché depuis) |
| 3 | `etache_pilotage_2026-10-11` + `pilotage_dynamique_et_pdf` | Page **Pilotage et analyses** (F8+F12) : périodes année/trimestre/mois, périmètre axe/direction, graphiques **SVG côté serveur**, synthèse par modèles de phrases ou **IA**, génération dynamique (AJAX), historique, exports Excel + note PDF, instantanés ; commande de **remise à neuf** `etache:reinitialiser` | migration 53 ; `config/synthese.php` ; `.env` clé API ; WAMP : `cacert.pem` dans les deux `php.ini` (CLI et Apache) |
| 4 | `plan_de_recette_etache.xlsx` | 177 tests / 24 modules, notes pondérées /20, procédure de remise à neuf, jeu de données | recette **à faire par l'utilisateur** |
| 5 | `regles_courrier_simplifie_2026-10-12` | Accès, renvoi au DG/imputeur, validateur par chaîne complète, retrait de l'agent des actions/sous-tâches, **recherche dans les listes** (`select-recherche.js/css`), texte « Archiver » | ajouter 2 lignes (CSS/JS) dans le gabarit pour activer la recherche partout |
| 6 | `parcours_dossier_v2` (+ `ParcoursCourrier.php` corrigé) | Carte **« Où en est ce courrier ? »** sur la fiche du courrier (détenteur, circuit de traitement, circuit de réponse) ; pagination de « Mes courriers à traiter » | aucune |
| 7 | `annulation_reponse_2026-10-12` | Annulation d'une réponse avec accord du dernier visa | migration 54 ; **2 routes à ajouter** (`reponse.annulation.accepter/refuser`) |
| 8 | `_cloche.blade.php` | Cloche : 10 notifications max, liste défilante, pied fixe, alignement/fond forcés | aucune |
| 9 | `validation_cloture_taches_2026-10-12` | Réglage **Clôture des tâches** + circuit valider/refuser/resoumettre, relance, contrôle à la clôture d'exercice | migration 55 ; **3 routes** (`tache.cloture.*`, FQCN) ; **1 ligne de planificateur** ; 1 ligne à changer dans `AnalysesPilotage` |
| 10 | `courrier_confidentiel_2026-10-13` | Contenu masqué pour les non désignés, fichiers de réponse protégés, circuit de visa = désigné | aucune |
| 11 | `imputation_confidentiel_2026-10-13` | Case de confirmation à l'imputation d'un courrier confidentiel + réglage « réimputation réservée au DG » | **ajouter la clé** `courrier.reimputation_confidentiel_dg_seul` dans `Reglages.php` |
| 12 | `errors/403.blade.php` | Page 403 du thème (français, message dynamique) | placer sous `resources/views/errors/` |
| 13 | `master.blade.php` | Menu **horizontal imposé** (HTML + neutralisation de `?layout=` et du stockage navigateur) | remplacer le fichier |

**Migrations à lancer (si pas déjà fait) : 47 → 55.**
**Planificateur (cron `schedule:run` chaque minute ; en local `schedule:work`)** : `courrier:alertes-diligences` 07:00, `rh:alertes` 07:10, `taches:relance-validation` 07:20, `pilotage:instantane` 23:50, `pilotage:synthese-hebdo` lundi 07:30.
**À vérifier chez l'utilisateur** : que chaque livraison a bien été appliquée, en particulier les routes ajoutées à la main et les lignes de réglages.

Corrections de bugs trouvés en test (déjà expliquées) : ligne `ligne.appendChild(nom); ligne.appendChild(choix);` manquante dans `js_imputation` (« champ role obligatoire ») ; `visasDuCycle()` est une méthode, pas une relation (`with()` interdit) ; `parent_diligence_id` est la clé de la diligence parente.

---

## 4. Travail en cours / questions ouvertes
1. **Visibilité des tâches (liste, matrice, calendrier, Gantt) ouvertes à tous en lecture** : l'utilisateur est d'accord sur le principe. Aujourd'hui `visiblePar` : ADMIN/DG/RD voient tout, les autres seulement leurs tâches ; **la fiche d'une tâche ne vérifie pas cette règle** (incohérence). Proposition : réglage « Visibilité des tâches » (Restreinte par défaut / Direction / Toutes), budget et justifications masqués selon le profil, exports alignés. **3 questions en attente** : ouvrir à tous ou à la direction ? budget réservé (ADMIN, DG, RD, direction porteuse) ? justifications de retard visibles de tous ? Fichiers à demander : `tache-list`, `tache-matrice`, `tache-calendar`, `tache-gantt`, `ExportTaches.php`.
2. **OCR au formulaire d'enregistrement d'un courrier arrivée** (en pause). Plan : interrupteur « Lire le scan automatiquement », OCR **dans le navigateur de l'agent** (Tesseract.js + PDF.js, données de langue française embarquées, aucune installation serveur, rien ne sort de la structure), détection de l'expéditeur, de l'objet, de la référence, de la date, du type, de l'urgence, du nombre de pièces et de la mention « confidentiel » ; champs remplis marqués « lu par OCR », modifiables. Le composant s'injecte par JS (peu de modifications de `js_courrier.blade.php`). Option ultérieure : **recherche plein texte** dans le contenu des scans (texte extrait stocké) ; un service OCR en ligne n'est pas retenu par défaut (confidentialité).
3. **Décisions à trancher** : marquer une diligence « traitée » avec des actions non faites (avertir, état actuel, ou bloquer) ; exiger une réponse signée quand l'instruction est « Courrier réponse à rédiger » ; bouton « Retirer pour correction » d'une réponse déjà soumise (non fait).
4. Pages d'erreur 404, 419, 500 du thème à adapter comme la 403 (l'utilisateur doit envoyer ses fichiers).

## 5. Lots restants (feuille de route)
| Chantier | État |
|---|---|
| **Recette de tout l'application** | À faire par l'utilisateur avec `plan_de_recette_etache.xlsx` après `etache:reinitialiser --niveau=tout` ; il enverra les anomalies par petits lots (n° de test, profil, message exact) |
| **F11 congés / absences** | En attente des règles de la DRH (barèmes, report, fractionnement, signataires, contractuels, loi 2023) : je prépare la liste de questions si demandé |
| **F13 fin** | Sauvegardes, HTTPS, mise en production (hébergement limité : limites PHP, pas d'accès serveur) |
| Reporté | Pré-tri du cabinet (optionnel) |

---

## 6. Pièges techniques connus (à ne pas redécouvrir)
- jQuery non chargé quand les scripts inline s'exécutent : lier les évènements en JS natif ; `$.fn.modal` est redéfini dans `layout/script.blade.php` : utiliser `$('#modal').modal('show')` et déplacer le modal sous `<body>`.
- `@json(...)` de Blade se casse sur les virgules : calculer dans `@php` puis `@json($variable)`. Apostrophe dans `@section('title', '...')` : utiliser la concaténation. Directive Blade collée à une lettre ASCII non compilée.
- Le thème force des styles sur le **dernier `<li>`** de la cloche, et certains `<span>` en ligne.
- **Menu** : le HTML de `master` était en `compact-wrapper` et `customizer.js` basculait en horizontal ; maintenant imposé dans le HTML.
- `Service::direction()` retourne un objet, pas une relation (pas de `with('service.direction')`). `statut_validation` des sous-tâches par défaut `validee`. `Courrier::STATUTS` : enregistre (À imputer), impute, en_traitement, solde, archive.
- Morph map : tout nouveau modèle journalisé doit être déclaré (`synthese_pilotage`, etc.). `Reglages::get()` : clés ajoutées `synthese.*`, `cloture_taches.*`, `exercice.controle_taches_a_valider`, `courrier.reimputation_confidentiel_dg_seul`.
- Dans WAMP il y a **deux `php.ini`** (CLI et Apache) : les deux doivent contenir `curl.cainfo` et `openssl.cafile` pour les appels HTTPS (IA).
- `Illuminate\Support\Str::lower/after/limit` et `collect()` ne sont pas disponibles dans mes tests simulés sans substituts : les ajouter aux harnais.

## 7. Comment reprendre
1. Coller cette fiche, joindre les **versions actuelles** des fichiers concernés par le chantier choisi.
2. Chantier suggéré en premier : la **visibilité des tâches** (3 réponses attendues), puis les **décisions** du §4.3, puis l'OCR.
3. En parallèle, l'utilisateur déroule la **recette** et envoie les anomalies par modules (ACC, SEC, PAR, ORG en premier).
