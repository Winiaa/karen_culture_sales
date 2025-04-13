<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration settings for the application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various routes and actions.
    |
    */
    'rate_limits' => [
        'login' => [
            'attempts' => 5,
            'decay_minutes' => 1,
        ],
        'password_reset' => [
            'attempts' => 3,
            'decay_minutes' => 1,
        ],
        'api' => [
            'attempts' => 60,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Configure session security settings.
    |
    */
    'session' => [
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'http_only' => true,
        'same_site' => 'lax',
        'lifetime' => 120, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    |
    | Configure password policy settings.
    |
    */
    'password' => [
        'min_length' => 8,
        'require_mixed_case' => true,
        'require_numbers' => true,
        'require_symbols' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    |
    | Configure CSRF protection settings.
    |
    */
    'csrf' => [
        'lifetime' => 120, // minutes
        'excluded_paths' => [
            'api/*',
            'webhooks/*',
        ],
    ],
]; 