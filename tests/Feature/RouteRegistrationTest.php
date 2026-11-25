<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use Illuminate\Support\Facades\Route;
use UplinkrApi\Http\Middleware\AuthenticateApiKey;
use UplinkrApi\Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_package_registers_expected_api_and_docs_routes(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->map(static fn ($route): array => [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
            ]);

        $this->assertTrue($routes->contains(
            static fn (array $route): bool => $route['uri'] === 'api/v1/projects'
                && in_array('GET', $route['methods'], true)
        ));
        $this->assertTrue($routes->contains(
            static fn (array $route): bool => $route['uri'] === 'api/v1/settings/iam-alive'
                && in_array('PATCH', $route['methods'], true)
        ));
        $this->assertTrue($routes->contains(
            static fn (array $route): bool => $route['uri'] === 'api/v1/projects/{project}/alerts'
                && in_array('GET', $route['methods'], true)
        ));
        $this->assertTrue($routes->contains(
            static fn (array $route): bool => $route['uri'] === 'api/v1/projects/{project}/alerts'
                && in_array('PATCH', $route['methods'], true)
        ));
        $this->assertTrue(Route::has('uplinkr-api.docs.ui'));
        $this->assertTrue(Route::has('uplinkr-api.docs.openapi'));
    }

    public function test_api_routes_are_protected_by_api_key_middleware(): void
    {
        $route = Route::getRoutes()->match(request()->create('/api/v1/projects', 'GET'));
        $middleware = $route->gatherMiddleware();

        $this->assertContains(AuthenticateApiKey::class, $middleware);
    }
}
