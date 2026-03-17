<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Support;

use Uplinkr\Handler\Project\InitHandler;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use Uplinkr\Storage\FileSettingsStorage;

class ApiTestData
{
    public static function authHeaders(): array
    {
        return [
            'X-Uplinkr-Api-Key' => 'test-api-key',
        ];
    }

    public static function projectPayload(
        string $project = 'status-page',
        ?string $label = 'Status Page',
        ?string $description = 'Public status page monitoring'
    ): array {
        return [
            'project' => $project,
            'label' => $label,
            'description' => $description,
        ];
    }

    public static function probePayload(
        string $url = 'https://example.com/health',
        string $method = 'GET',
        ?string $project = null
    ): array {
        $payload = [
            'url' => $url,
            'method' => $method,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => '{"ping":true}',
        ];

        if ($project !== null) {
            $payload['project'] = $project;
        }

        return $payload;
    }

    public static function iamAlivePayload(): array
    {
        return [
            'enabled' => true,
            'interval_hours' => 6,
            'channels' => ['mail', 'webhook'],
        ];
    }

    public static function seedProject(InitHandler $initHandler, array $payload): void
    {
        $initHandler->handle($payload);
    }

    public static function seedProbe(AddProbeHandler $addProbeHandler, array $payload): void
    {
        $addProbeHandler->handle($payload);
    }

    public static function seedIamAliveSettings(FileSettingsStorage $settingsStorage, array $payload): void
    {
        $settingsStorage->updateIamAliveSettings(
            $payload['enabled'] ?? null,
            $payload['interval_hours'] ?? null,
            $payload['channels'] ?? null,
        );
    }
}
