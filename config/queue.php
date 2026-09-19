<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue Connections — driver `database` en local
    |--------------------------------------------------------------------------
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
