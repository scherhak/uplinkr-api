<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use UplinkrApi\Tests\TestCase;

class SettingsApiTest extends TestCase
{
    public function test_can_read_default_iam_alive_settings(): void
    {
        $response = $this
            ->withHeaders(['X-Uplinkr-Api-Key' => 'test-api-key'])
            ->getJson('/api/v1/settings/iam-alive');

        $response->assertOk()->assertJson([
            'data' => [
                'enabled' => false,
                'interval_hours' => 24,
                'channels' => ['mail'],
                'last_sent_at' => null,
                'last_attempted_at' => null,
            ],
        ]);
    }

    public function test_can_update_iam_alive_settings(): void
    {
        $payload = [
            'enabled' => true,
            'interval_hours' => 6,
            'channels' => ['mail', 'webhook'],
        ];

        $response = $this
            ->withHeaders(['X-Uplinkr-Api-Key' => 'test-api-key'])
            ->patchJson('/api/v1/settings/iam-alive', $payload);

        $response->assertOk()->assertJson([
            'data' => [
                'enabled' => true,
                'interval_hours' => 6,
                'channels' => ['mail', 'webhook'],
            ],
        ]);

        $this
            ->withHeaders(['X-Uplinkr-Api-Key' => 'test-api-key'])
            ->getJson('/api/v1/settings/iam-alive')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'enabled' => true,
                    'interval_hours' => 6,
                    'channels' => ['mail', 'webhook'],
                ],
            ]);
    }
}
