<?php

declare(strict_types=1);

namespace UplinkrApi\Tests;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Uplinkr\UplinkrServiceProvider;
use UplinkrApi\UplinkrApiServiceProvider;

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
}
