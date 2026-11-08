<?php

/*
|--------------------------------------------------------------------------
| Thème des exports PDF et Excel de e-Tâche
|--------------------------------------------------------------------------
| UN SEUL ENDROIT pour changer l'apparence de tous les PDF et Excel.
| Couleurs en hexadécimal, avec ou sans « # ».
| Après modification :  php artisan config:clear
*/

return [

    'application' => 'e-Tâche',

    'couleurs' => [
        // --- Couleurs principales ---------------------------------------
        'primaire'        => '1B62AB', // bleu : titres, en-têtes de tableaux, bandeau
        'accent'          => 'F7941D', // orange : traits sous les titres, barres des cartes, bandeau
        'accent_texte'    => 'C2570A', // orange foncé : lignes de filtres (lisible sur fond blanc)

        // --- Texte --------------------------------------------------------
        'texte'           => '1F2933', // texte courant
        'texte_doux'      => '6B7280', // texte secondaire (« Exporté le… », pied de page)
        'entete_texte'    => 'FFFFFF', // texte des en-têtes de tableaux

        // --- Surfaces -----------------------------------------------------
        'fond_carte'      => 'F1F7F3', // fond des cartes de chiffres clés
        'bordure_carte'   => 'DCE9E1', // contour des cartes
        'ligne_alternee'  => 'EEF6F1', // 1 ligne sur 2 des tableaux
        'ligne_bordure'   => 'E3E8E5', // filet sous chaque ligne

        // --- États --------------------------------------------------------
        'danger'          => 'C0392B', // retards, valeurs d'alerte

        // --- Fiche de transmission du courrier (modèle papier) -------------
        'fiche_bandeau'   => '6B6B6B', // bandeaux gris de la fiche (garder gris pour rester fidèle au papier)
    ],

    // Couleur de la barre à gauche des cartes de chiffres clés (répétée en boucle).
    // Valeurs possibles : une clé de 'couleurs' ci-dessus, ou un code hexadécimal.
    'cartes' => ['primaire', 'accent', 'primaire', 'accent'],

    'tailles' => [
        'texte' => 12, 'tableau' => 12, 'entete' => 12, 'infos' => 11, 'titre' => 26,
        'section' => 14, 'carte_libelle' => 10, 'carte_valeur' => 19, 'pied' => 9,
    ],

    // Bandeau tout en haut de chaque page : part de l'accent (orange), le reste est en primaire (vert).
    'bandeau' => [
        'hauteur_pdf'    => 9,   // en pixels
        'part_accent'    => 25,  // en % de la largeur
    ],

    // Polices
    //   PDF   : Helvetica (légère, comme le modèle eGAD) ; mettre « DejaVu Sans » si un caractère
    //           spécial s'affiche mal (≥, →, ☒…).
    //   Excel : police du classeur.
    'police_pdf'   => 'Helvetica',
    'police_excel' => 'Calibri',
];
