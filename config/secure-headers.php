<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    */

    'hsts' => [
        'enable' => env('SECURE_HEADERS_HSTS', true),
        'max_age' => env('SECURE_HEADERS_HSTS_MAX_AGE', 31536000), // 1 year
        'include_subdomains' => env('SECURE_HEADERS_HSTS_SUBDOMAINS', true),
        'preload' => env('SECURE_HEADERS_HSTS_PRELOAD', true),
    ],

    'csp' => [
        'enable' => env('SECURE_HEADERS_CSP', true),
        'default_src' => "'self'",
        'script_src' => "'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com",
        'style_src' => "'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com",
        'font_src' => "'self' fonts.gstatic.com",
        'img_src' => "'self' data: blob:",
        'connect_src' => "'self'",
        'frame_ancestors' => "'self'",
        'base_uri' => "'self'",
        'form_action' => "'self'",
    ],

    'referrer_policy' => env('SECURE_HEADERS_REFERRER_POLICY', 'strict-origin-when-cross-origin'),

    'permissions_policy' => [
        'camera' => '(self)',
        'microphone' => '()',
        'geolocation' => '(self)',
        'payment' => '()',
        'usb' => '()',
    ],

    'x_frame_options' => env('SECURE_HEADERS_X_FRAME_OPTIONS', 'SAMEORIGIN'),
    'x_content_type_options' => env('SECURE_HEADERS_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
    'x_xss_protection' => env('SECURE_HEADERS_X_XSS_PROTECTION', '1; mode=block'),

    'remove_headers' => [
        'X-Powered-By',
        'Server',
        'X-Generator',
    ],
];