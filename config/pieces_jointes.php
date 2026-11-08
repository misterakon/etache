<?php

/*
 | Pièces jointes (livrables, puis courrier, dossier de l'agent...).
 | Partie technique uniquement (disque, correspondance extension / type MIME, aperçu).
 */

$word = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-zip-compressed'];
$excel = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/x-zip-compressed'];
$ppt = ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/x-zip-compressed'];
$ole = ['application/CDFV2', 'application/x-ole-storage', 'application/vnd.ms-office'];

return [
    'disque' => 'local',                  // stockage privé : jamais accessible par une adresse publique
    // La taille maximale, le nombre de fichiers et les types autorisés se règlent dans l'écran Réglages (module « Fichiers »).

    // extension autorisée => types MIME acceptés (le contenu réel est vérifié, pas seulement le nom)
    'extensions' => [
        'pdf' => ['application/pdf'],
        'doc' => array_merge(['application/msword'], $ole),
        'docx' => $word,
        'xls' => array_merge(['application/vnd.ms-excel'], $ole),
        'xlsx' => $excel,
        'ppt' => array_merge(['application/vnd.ms-powerpoint'], $ole),
        'pptx' => $ppt,
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
    ],

    'apercu' => ['pdf', 'jpg', 'jpeg', 'png'],   // affichables dans le navigateur
];
