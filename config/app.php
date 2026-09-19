<?php

return [

    'name' => env('APP_NAME', 'PharmaConnect'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost:8000'),

    'timezone' => env('APP_TIMEZONE', 'Africa/Douala'),

    'locale' => env('APP_LOCALE', 'fr'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'fr'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'fr_CM'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres métier PharmaConnect
    |--------------------------------------------------------------------------
    */

    'frais_livraison_defaut' => (int) env('PHARMA_FRAIS_LIVRAISON', 1000),

    'minutes_livraison_defaut' => (int) env('CHRONO_DEFAUT_LIVRAISON', 35),

    'seuil_stock_bas' => (int) env('PHARMA_SEUIL_STOCK_BAS', 5),

    'mois_alerte_peremption' => (int) env('PHARMA_MOIS_ALERTE_PEREMPTION', 3),
];
