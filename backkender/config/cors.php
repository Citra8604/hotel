<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | JURUS SAPU JAGAT: Buka semua gerbang biar Frontend Nuxt bisa bebas masuk!
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Bintang (*) artinya izinin semua URL (localhost:3000, dll)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Matiin ini biar allowed_origins => ['*'] bisa jalan

];