<?php

return [
    'route' => [
        'prefix' => env('UPLINKR_API_ROUTE_PREFIX', 'api/v1'),
        'middleware' => ['api'],
    ],
];
