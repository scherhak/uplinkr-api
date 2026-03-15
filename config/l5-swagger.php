<?php

declare(strict_types=1);

$annotationPaths = array_values(array_filter([
    realpath(__DIR__ . '/../src'),
    base_path('vendor/scherhak/uplinkr-api/src'),
]));

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Uplinkr API',
            ],
            'routes' => [
                'api' => 'api/docs',
            ],
            'paths' => [
                'use_absolute_path' => true,
                'swagger_ui_assets_path' => 'vendor/swagger-api/swagger-ui/dist/',
                'docs_json' => 'uplinkr-api-docs.json',
                'docs_yaml' => 'uplinkr-api-docs.yaml',
                'format_to_use_for_docs' => 'json',
                'annotations' => $annotationPaths,
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'api/docs/openapi',
            'oauth2_callback' => 'api/docs/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],
        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'excludes' => [],
        ],
        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
            'open_api_spec_version' => \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION,
        ],
        'securityDefinitions' => [
            'securitySchemes' => [
                'uplinkrApiKey' => [
                    'type' => 'apiKey',
                    'description' => 'Uplinkr API key header',
                    'name' => 'X-Uplinkr-Api-Key',
                    'in' => 'header',
                ],
            ],
            'security' => [
                [
                    'uplinkrApiKey' => [],
                ],
            ],
        ],
        'generate_always' => (bool) env('UPLINKR_API_SWAGGER_GENERATE_ALWAYS', true),
        'generate_yaml_copy' => false,
        'proxy' => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url' => null,
        'ui' => [
            'display' => [
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'list'),
                'filter' => true,
            ],
            'authorization' => [
                'persist_authorization' => true,
            ],
        ],
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('APP_URL', 'http://localhost'),
        ],
    ],
];
