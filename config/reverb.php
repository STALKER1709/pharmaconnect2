<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serveur Reverb — temps réel 100 % local
    |--------------------------------------------------------------------------
    | Lancer avec : php artisan reverb:start
    */

    'default' => env('REVERB_SERVER', 'reverb'),

    'apps' => [
        'provider' => 'config',
        'apps' => [
            [
                'key' => env('REVERB_APP_KEY', 'pharmaconnect-local-key'),
                'secret' => env('REVERB_APP_SECRET', 'pharmaconnect-local-secret'),
                'app_id' => env('REVERB_APP_ID', 'pharmaconnect'),
                'options' => [
                    'host' => '127.0.0.1',
                    'port' => 8080,
                    'scheme' => 'http',
                ],
                'allowed_origins' => ['*'],
                'ping_interval' => 25,
                'max_message_size' => 1e6,
            ],
        ],
    ],

    'server' => [
        'reverb' => [
            'host' => '0.0.0.0',
            'port' => 8080,
            'path' => env('REVERB_SERVER_PATH', ''),
            'hostname' => 'localhost',
            'options' => [
                'persistent' => true,
            ],
        ],

        'factory' => [
            'loop' => env('REVERB_LOOP', \React\EventLoop\Loop::get()),
            'max_requests' => 1000,
        ],

        'scheduler' => [
            'ping' => [
                'interval' => (int) env('REVERB_APP_PING_INTERVAL', 25),
            ],
            'monitor' => [
                'interval' => (int) env('REVERB_APP_MONITOR_INTERVAL', 60),
            ],
        ],
    ],

];
