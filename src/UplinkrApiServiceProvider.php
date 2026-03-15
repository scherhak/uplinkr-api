<?php

declare(strict_types=1);

namespace UplinkrApi;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class UplinkrApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiter();

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->publishes([
            __DIR__ . '/../config/uplinkr-api.php' => config_path('uplinkr-api.php'),
        ], 'uplinkr-api-config');

        $this->publishes([
            __DIR__ . '/../config/l5-swagger.php' => config_path('l5-swagger.php'),
        ], 'uplinkr-api-swagger-config');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'uplinkr-api');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/uplinkr-api'),
        ], 'uplinkr-api-lang');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/uplinkr-api.php', 'uplinkr-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/l5-swagger.php', 'l5-swagger');
    }

    private function configureRateLimiter(): void
    {
        $key = (string) config('uplinkr-api.security.rate_limit.key', 'uplinkr-api');

        RateLimiter::for($key, function (Request $request): Limit {
            if (!(bool) config('uplinkr-api.security.rate_limit.enabled', true)) {
                return Limit::none();
            }

            $maxAttempts = max(1, (int) config('uplinkr-api.security.rate_limit.max_attempts', 60));
            $decayMinutes = max(1, (int) config('uplinkr-api.security.rate_limit.decay_minutes', 1));
            $byIp = (bool) config('uplinkr-api.security.rate_limit.by_ip', true);

            $limit = Limit::perMinutes($decayMinutes, $maxAttempts);
            if ($byIp) {
                return $limit->by((string) $request->ip());
            }

            return $limit;
        });
    }
}
