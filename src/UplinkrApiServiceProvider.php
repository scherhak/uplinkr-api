<?php

declare(strict_types=1);

namespace UplinkrApi;

use Dedoc\Scramble\Scramble;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class UplinkrApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiter();
        $this->configureApiDocs();

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->publishes([
            __DIR__ . '/../config/uplinkr-api.php' => config_path('uplinkr-api.php'),
        ], 'uplinkr-api-config');

        $this->publishes([
            __DIR__ . '/../config/scramble.php' => config_path('scramble.php'),
        ], 'uplinkr-api-docs-config');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'uplinkr-api');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/uplinkr-api'),
        ], 'uplinkr-api-lang');
    }

    public function register(): void
    {
        Scramble::ignoreDefaultRoutes();

        $this->mergeConfigFrom(__DIR__ . '/../config/uplinkr-api.php', 'uplinkr-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/scramble.php', 'scramble');
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

    private function configureApiDocs(): void
    {
        $apiPrefix = trim((string) config('uplinkr-api.route.prefix', 'api/v1'), '/');

        Scramble::routes(static function ($route) use ($apiPrefix): bool {
            $uri = trim((string) $route->uri(), '/');
            $actionName = (string) $route->getActionName();

            return str_starts_with($uri, $apiPrefix)
                && str_contains($actionName, 'UplinkrApi\\Http\\Controllers\\Api\\');
        });

        Scramble::registerUiRoute(path: 'api/docs')->name('uplinkr-api.docs.ui');
        Scramble::registerJsonSpecificationRoute(path: 'api/docs/openapi')->name('uplinkr-api.docs.openapi');
    }
}
