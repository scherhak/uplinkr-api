<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use UplinkrApi\Tests\Support\ApiTestData;
use UplinkrApi\Tests\TestCase;

class SettingsApiTest extends TestCase
{
    public function test_get_iam_alive_settings_returns_current_settings(): void
    {
        $this->seedIamAliveSettings(ApiTestData::iamAlivePayload());

        $this->withHeaders($this->apiHeaders())
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

    public function test_patch_iam_alive_settings_updates_current_settings(): void
    {
        $this->withHeaders($this->apiHeaders())
            ->patchJson('/api/v1/settings/iam-alive', ApiTestData::iamAlivePayload())
            ->assertOk()
            ->assertJson([
                'data' => [
                    'enabled' => true,
                    'interval_hours' => 6,
                    'channels' => ['mail', 'webhook'],
                ],
            ]);

        $this->withHeaders($this->apiHeaders())
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
