<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept'],
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:8080,http://127.0.0.1:8080')),
    'allowed_origins_patterns' => [],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,
];
