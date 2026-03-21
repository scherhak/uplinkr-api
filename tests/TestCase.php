<?php

declare(strict_types=1);

namespace UplinkrApi\Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Uplinkr\Handler\Probe\ResultHandler;
use Uplinkr\Handler\Probe\UrlHandler;
use Uplinkr\Handler\Project\InitHandler;
use Uplinkr\Handler\Project\Probes\ProbeAllProjectsHandler;
use Uplinkr\Handler\Project\Probes\ProbeSelectedProjectsHandler;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use Uplinkr\Interfaces\ProbeResultsStorageInterface;
use Uplinkr\Interfaces\ProjectStorageInterface;
use Uplinkr\Objects\Config\UplinkrConfig;
use Uplinkr\Storage\FileSettingsStorage;
use Uplinkr\Support\Sanitizer;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Uplinkr\UplinkrServiceProvider;
use UplinkrApi\UplinkrApiServiceProvider;
use UplinkrApi\Tests\Support\ApiTestData;

abstract class TestCase extends OrchestraTestCase
{
    private string $storagePath = 'uplinkr-api-tests';

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

        $this->storagePath = sprintf(
            'uplinkr-api-tests/%s',
            strtolower(preg_replace('/[^a-z0-9]+/', '-', $this->name()) ?? 'test')
        );

        config()->set('uplinkr.storage.path', $this->storagePath);
        $this->resetUplinkrSingletons();
        $this->safeDeleteStoragePath();
    }

    protected function tearDown(): void
    {
        $this->safeDeleteStoragePath();

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

    protected function uniqueProjectName(string $prefix = 'project'): string
    {
        return sprintf('%s-%s', $prefix, bin2hex(random_bytes(4)));
    }

    protected function storagePath(): string
    {
        return $this->storagePath;
    }

    private function resetUplinkrSingletons(): void
    {
        foreach ([
            UplinkrConfig::class,
            Sanitizer::class,
            ProjectStorageInterface::class,
            ProbeResultsStorageInterface::class,
            ResultHandler::class,
            UrlHandler::class,
            ProbeSelectedProjectsHandler::class,
            ProbeAllProjectsHandler::class,
            FileSettingsStorage::class,
        ] as $abstract) {
            if ($this->app->bound($abstract)) {
                $this->app->forgetInstance($abstract);
            }
        }
    }

    private function safeDeleteStoragePath(): void
    {
        try {
            Storage::disk('local')->deleteDirectory($this->storagePath);
        } catch (\Throwable) {
            // Ignore storage cleanup issues in tests to keep teardown stable.
        }
    }
}
