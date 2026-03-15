<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use UplinkrApi\Http\Controllers\Api\ProbesController;
use UplinkrApi\Http\Controllers\Api\ProjectsController;
use UplinkrApi\Http\Middleware\AuthenticateApiKey;

$prefix = (string) config('uplinkr-api.route.prefix', 'api/v1');
$middleware = config('uplinkr-api.route.middleware', ['api']);
$middleware = is_array($middleware) ? $middleware : [$middleware];

if ((bool) config('uplinkr-api.security.api_key.enabled', true)) {
    $middleware[] = AuthenticateApiKey::class;
}

if ((bool) config('uplinkr-api.security.rate_limit.enabled', true)) {
    $rateLimitKey = (string) config('uplinkr-api.security.rate_limit.key', 'uplinkr-api');
    $middleware[] = sprintf('throttle:%s', $rateLimitKey);
}

Route::prefix(trim($prefix, '/'))
    ->middleware($middleware)
    ->group(function (): void {
        Route::get('/projects', [ProjectsController::class, 'index']);
        Route::post('/projects', [ProjectsController::class, 'store']);
        Route::get('/projects/{project}', [ProjectsController::class, 'show']);
        Route::patch('/projects/{project}', [ProjectsController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectsController::class, 'destroy']);

        Route::get('/probes', [ProbesController::class, 'index']);
        Route::post('/probes', [ProbesController::class, 'store']);

        Route::get('/projects/{project}/probes', [ProbesController::class, 'projectIndex']);
        Route::post('/projects/{project}/probes', [ProbesController::class, 'projectStore']);
        Route::get('/projects/{project}/probes/{probeId}', [ProbesController::class, 'show']);
        Route::patch('/projects/{project}/probes/{probeId}', [ProbesController::class, 'update']);
        Route::delete('/projects/{project}/probes/{probeId}', [ProbesController::class, 'destroy']);

    });
