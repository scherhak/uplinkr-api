<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use UplinkrApi\Tests\TestCase;

class AuthenticationApiTest extends TestCase
{
    public function test_request_without_api_key_returns_unauthorized(): void
    {
        $this->getJson('/api/v1/projects')
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthorized',
            ]);
    }

    public function test_request_with_invalid_api_key_returns_unauthorized(): void
    {
        $this->withHeaders([
            'X-Uplinkr-Api-Key' => 'invalid-api-key',
        ])->getJson('/api/v1/projects')
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthorized',
            ]);
    }

    public function test_request_with_bearer_token_is_accepted_when_enabled(): void
    {
        config()->set('uplinkr-api.security.api_key.accept_bearer_token', true);

        $this->withHeaders([
            'Authorization' => 'Bearer test-api-key',
        ])->getJson('/api/v1/projects')
            ->assertOk();
    }

    public function test_request_with_bearer_token_is_rejected_when_disabled(): void
    {
        config()->set('uplinkr-api.security.api_key.accept_bearer_token', false);

        $this->withHeaders([
            'Authorization' => 'Bearer test-api-key',
        ])->getJson('/api/v1/projects')
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthorized',
            ]);
    }

    public function test_request_is_rate_limited_after_max_attempts_are_exceeded(): void
    {
        config()->set('uplinkr-api.route.prefix', 'api/throttled-v1');
        config()->set('uplinkr-api.security.rate_limit.enabled', true);
        config()->set('uplinkr-api.security.rate_limit.max_attempts', 1);
        config()->set('uplinkr-api.security.rate_limit.decay_minutes', 1);
        config()->set('uplinkr-api.security.rate_limit.by_ip', true);

        require dirname(__DIR__, 2) . '/routes/api.php';

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/throttled-v1/projects')
            ->assertOk();

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/throttled-v1/projects')
            ->assertStatus(429);
    }
}
