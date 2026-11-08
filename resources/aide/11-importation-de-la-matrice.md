---
titre: Importation de la matrice
icone: fa-solid fa-file-import
couleur: orange
resume: Charger axes, actions et tâches depuis un classeur Excel, après contrôle ligne par ligne.
roles: ADMIN, DG
routes: feuille_de_route.import_matrice.*
ordre: 9
---
L'**importation de la matrice** (Feuille de route › Importation matrice) crée en une seule opération les axes, actions clés, actions mesurables et tâches d'un exercice à partir d'un fichier Excel.

> **À savoir** L'import est **« tout ou rien »** : une seule erreur le bloque et **rien n'est enregistré** tant que le fichier n'est pas corrigé. Il est aussi **sans doublon** : ce qui existe déjà n'est pas recréé.

## Préparer le fichier

1. Dans la carte **Télécharger le modèle**, récupérez le classeur prêt à remplir : il contient les valeurs de votre application.
2. Remplissez-le : **une ligne = une tâche**.
3. Les valeurs d'axe, d'action clé et d'action mesurable peuvent n'être écrites **qu'une seule fois** (cellules fusionnées) : elles s'appliquent aux lignes suivantes.

> **Astuce** Gardez le modèle tel quel : ne renommez pas les colonnes et n'ajoutez pas de ligne de titre.

## Analyser le fichier

1. Dans **Analyser votre fichier**, choisissez l'**exercice** : les éléments seront créés dans celui-ci.
2. Déposez le fichier, ou cliquez sur la zone de dépôt pour le choisir.
3. Cliquez sur **Analyser**. Cette étape **ne modifie rien**.

## Lire la vérification

Le tableau de **Vérification du fichier** montre chaque ligne avec son résultat. Filtrez avec les boutons :

- **Toutes** les lignes ;
- les lignes en **erreur** (à corriger) ;
- les **nouvelles** lignes (qui seront créées) ;
- les lignes **existantes** (déjà présentes, ignorées).

Pour chaque erreur, le message indique la ligne et la raison (date invalide, code inconnu, libellé manquant…). Corrigez le fichier Excel, puis recommencez l'analyse.

## Lancer l'import

Quand il n'y a plus d'erreur, le bouton **Importer** devient actif. Cliquez dessus : une barre de progression suit l'opération.

> **Attention** Ne fermez pas la page pendant l'importation. À la fin, un résumé indique le nombre d'éléments créés.
