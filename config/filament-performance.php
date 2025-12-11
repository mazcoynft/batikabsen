<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Performance Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('FILAMENT_CACHE_ENABLED', true),
        'ttl' => env('FILAMENT_CACHE_TTL', 300), // 5 minutes
        'store' => env('FILAMENT_CACHE_STORE', 'redis'), // redis, database, file
    ],

    'database' => [
        'query_cache' => env('FILAMENT_QUERY_CACHE', true),
        'connection_pooling' => env('FILAMENT_CONNECTION_POOLING', true),
        'lazy_loading' => env('FILAMENT_LAZY_LOADING', true),
    ],

    'security' => [
        'rate_limiting' => [
            'enabled' => env('FILAMENT_RATE_LIMITING', true),
            'login_attempts' => env('FILAMENT_LOGIN_RATE_LIMIT', 5),
            'general_requests' => env('FILAMENT_GENERAL_RATE_LIMIT', 100),
            'post_requests' => env('FILAMENT_POST_RATE_LIMIT', 30),
        ],
        
        'ip_whitelist' => [
            'enabled' => env('FILAMENT_IP_WHITELIST_ENABLED', false),
            'allowed_ips' => explode(',', env('FILAMENT_ALLOWED_IPS', '')),
        ],
        
        'session_security' => [
            'fingerprinting' => env('FILAMENT_SESSION_FINGERPRINTING', true),
            'regeneration_interval' => env('FILAMENT_SESSION_REGENERATION', 1800), // 30 minutes
        ],
    ],

    'monitoring' => [
        'performance_headers' => env('FILAMENT_PERFORMANCE_HEADERS', true),
        'query_logging' => env('FILAMENT_QUERY_LOGGING', false),
        'memory_monitoring' => env('FILAMENT_MEMORY_MONITORING', true),
    ],

    'optimization' => [
        'compression' => env('FILAMENT_COMPRESSION', true),
        'minification' => env('FILAMENT_MINIFICATION', true),
        'lazy_widgets' => env('FILAMENT_LAZY_WIDGETS', true),
    ],
];