<?php

declare(strict_types=1);

namespace UplinkrApi;

use Illuminate\Support\ServiceProvider;

class UplinkrApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->publishes([
            __DIR__ . '/../config/uplinkr-api.php' => config_path('uplinkr-api.php'),
        ], 'uplinkr-api-config');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'uplinkr-api');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/uplinkr-api'),
        ], 'uplinkr-api-lang');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/uplinkr-api.php', 'uplinkr-api');
    }
}
