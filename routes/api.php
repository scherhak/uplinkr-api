<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use UplinkrApi\Http\Controllers\Api\ProbesController;
use UplinkrApi\Http\Controllers\Api\ProjectsController;
use UplinkrApi\Http\Controllers\Api\ResultsController;

$prefix = (string) config('uplinkr-api.route.prefix', 'api/v1');
$middleware = config('uplinkr-api.route.middleware', ['api']);

Route::prefix(trim($prefix, '/'))
    ->middleware(is_array($middleware) ? $middleware : [$middleware])
    ->group(function (): void {
        Route::get('/projects', [ProjectsController::class, 'index']);
        Route::post('/projects', [ProjectsController::class, 'store']);

        Route::get('/probes', [ProbesController::class, 'index']);
        Route::post('/probes', [ProbesController::class, 'store']);

        Route::get('/results', [ResultsController::class, 'index']);
        Route::post('/results', [ResultsController::class, 'store']);
    });
