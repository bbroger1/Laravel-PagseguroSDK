<?php
return [
    'env' => env('PAGSEGURO_ENV'),
    'api_version' => env('PAGSEGURO_API_VERSION'),
    'sandbox' => [
        'email' => env('PAGSEGURO_EMAIL_SANDBOX'),
        'token' => env('PAGSEGURO_TOKEN_SANDBOX'),
        'endpoint' => env('PAGSEGURO_ENDPOINT_SANDBOX'),
    ],
    'production' => [
        'email' => env('PAGSEGURO_EMAIL_PRODUCTION'),
        'token' => env('PAGSEGURO_TOKEN_PRODUCTION'),
        'endpoint' => env('PAGSEGURO_ENDPOINT_PRODUCTION'),
    ],
    'public_key' => env('PAGSEGURO_PUBLIC_KEY'),
    'description' => '',
    'currency' => 'BRL',
];