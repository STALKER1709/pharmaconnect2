<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passerelles externes — implémentations MOCK par défaut
    |--------------------------------------------------------------------------
    | Aucun service cloud requis. Les variables PAYMENT_GATEWAY, SMS_GATEWAY
    | et CHATBOT_SERVICE choisissent l'implémentation liée dans AppServiceProvider.
    */

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'mock'),
        'momo_payee' => env('MOCK_MOMO_PAYEE_NUMBER', '670000000'),
        'orange_payee' => env('MOCK_ORANGE_PAYEE_NUMBER', '690000000'),
    ],

    'sms' => [
        'gateway' => env('SMS_GATEWAY', 'mock'),
        'from' => env('SMS_FROM', 'PharmaConnect'),
    ],

    'chatbot' => [
        'service' => env('CHATBOT_SERVICE', 'mock'),
    ],

];
