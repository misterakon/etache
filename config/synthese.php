<?php

/** Synthèses de pilotage par IA. La clé n'est lue QUE ici (compatible avec « php artisan config:cache »). */
return [
    'cle' => env('ANTHROPIC_API_KEY'),
    'url' => env('ANTHROPIC_API_URL', 'https://api.anthropic.com/v1/messages'),
    'version' => '2023-06-01',
    'timeout' => (int) env('ANTHROPIC_TIMEOUT', 45),
];
