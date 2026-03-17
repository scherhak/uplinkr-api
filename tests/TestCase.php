<?php

declare(strict_types=1);

namespace UplinkrApi\Tests;

use Illuminate\Support\Facades\Storage;
use Uplinkr\Handler\Project\InitHandler;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use Uplinkr\Storage\FileSettingsStorage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Uplinkr\UplinkrServiceProvider;
use UplinkrApi\UplinkrApiServiceProvider;
use UplinkrApi\Tests\Support\ApiTestData;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            UplinkrServiceProvider::class,
            UplinkrApiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('uplinkr.storage.disk', 'local');
        $app['config']->set('uplinkr.storage.path', 'uplinkr-api-tests');
        $app['config']->set('uplinkr-api.security.api_key.enabled', true);
        $app['config']->set('uplinkr-api.security.api_key.keys', ['test-api-key']);
        $app['config']->set('uplinkr-api.security.rate_limit.enabled', false);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::disk('local')->deleteDirectory('uplinkr-api-tests');
    }

    protected function tearDown(): void
    {
        Storage::disk('local')->deleteDirectory('uplinkr-api-tests');

        parent::tearDown();
    }

    protected function apiHeaders(): array
    {
        return ApiTestData::authHeaders();
    }

    protected function seedProject(array $payload): void
    {
        ApiTestData::seedProject($this->app->make(InitHandler::class), $payload);
    }

    protected function seedProbe(array $payload): void
    {
        ApiTestData::seedProbe($this->app->make(AddProbeHandler::class), $payload);
    }

    protected function seedIamAliveSettings(array $payload): void
    {
        ApiTestData::seedIamAliveSettings($this->app->make(FileSettingsStorage::class), $payload);
    }
}
