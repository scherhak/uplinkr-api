<?php

declare(strict_types=1);

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'api_path' => 'api/v1',
    'api_domain' => null,
    'export_path' => 'api/docs/openapi',
    'info' => [
        'version' => env('UPLINKR_API_VERSION', '1.0.0'),
        'description' => 'REST API for projects and probes in the Uplinkr ecosystem.',
    ],
    'ui' => [
        'title' => 'Uplinkr API Docs',
        'theme' => 'light',
        'hide_try_it' => false,
        'hide_schemas' => false,
        'logo' => '',
        'try_it_credentials_policy' => 'include',
        'layout' => 'responsive',
    ],
    'servers' => null,
    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,
    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],
    'extensions' => [],
];
