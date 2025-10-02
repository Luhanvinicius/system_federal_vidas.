<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://127.0.0.1:8000',
        'http://localhost:8000',
        'http://localhost:5173',
        'https://localhost:5173',
        'https://a5a47f0c4af0.ngrok-free.app',
    ],

    'allowed_origins_patterns' => [
        '/^https?:\/\/[a-z0-9\-]+\.ngrok(\-free)?\.app$/i',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
