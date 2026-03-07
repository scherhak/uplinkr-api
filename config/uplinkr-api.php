<?php

declare(strict_types=1);

return [
    'route' => [
        'prefix' => env('UPLINKR_API_ROUTE_PREFIX', 'api/v1'),
        'middleware' => ['api'],
    ],

    'security' => [
        'api_key' => [
            'enabled' => (bool) env('UPLINKR_API_AUTH_ENABLED', true),
            'header' => env('UPLINKR_API_AUTH_HEADER', 'X-Uplinkr-Api-Key'),
            'accept_bearer_token' => (bool) env('UPLINKR_API_ACCEPT_BEARER_TOKEN', true),
            'keys' => array_values(array_filter(array_map(
                static fn (string $value): string => trim($value),
                explode(',', (string) env('UPLINKR_API_KEYS', '')),
            ))),
        ],

        'rate_limit' => [
            'enabled' => (bool) env('UPLINKR_API_RATE_LIMIT_ENABLED', true),
            'key' => env('UPLINKR_API_RATE_LIMIT_KEY', 'uplinkr-api'),
            'max_attempts' => (int) env('UPLINKR_API_RATE_LIMIT_MAX_ATTEMPTS', 60),
            'decay_minutes' => (int) env('UPLINKR_API_RATE_LIMIT_DECAY_MINUTES', 1),
            'by_ip' => (bool) env('UPLINKR_API_RATE_LIMIT_BY_IP', true),
        ],
    ],
];
