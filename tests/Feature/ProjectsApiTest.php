<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use UplinkrApi\Tests\Support\ApiTestData;
use UplinkrApi\Tests\TestCase;

class ProjectsApiTest extends TestCase
{
    public function test_get_projects_returns_seeded_projects(): void
    {
        $this->seedProject(ApiTestData::projectPayload('alpha-project', 'Alpha', 'Alpha description'));
        $this->seedProject(ApiTestData::projectPayload('beta-project', 'Beta', 'Beta description'));

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.count', 2);
    }

    public function test_post_projects_creates_a_project(): void
    {
        $payload = ApiTestData::projectPayload('status-page', 'Status Page', 'Public status page monitoring');

        $this->withHeaders($this->apiHeaders())
            ->postJson('/api/v1/projects', $payload)
            ->assertCreated()
            ->assertJsonPath('data.project', 'status-page');

        $this->assertTrue(Storage::disk('local')->exists('uplinkr-api-tests/status-page/settings.json'));
    }

    public function test_get_project_returns_the_requested_project(): void
    {
        $this->seedProject(ApiTestData::projectPayload('status-page', 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/projects/status-page')
            ->assertOk()
            ->assertJsonPath('data.project', 'status-page')
            ->assertJsonPath('data.label', 'Status Page');
    }

    public function test_patch_project_updates_the_requested_project(): void
    {
        $this->seedProject(ApiTestData::projectPayload('status-page', 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->patchJson('/api/v1/projects/status-page', [
                'label' => 'Updated Status Page',
                'description' => 'Updated description',
                'status' => 'disabled',
            ])
            ->assertOk()
            ->assertJsonPath('data.project', 'status-page');

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/projects/status-page')
            ->assertOk()
            ->assertJsonPath('data.label', 'Updated Status Page')
            ->assertJsonPath('data.description', 'Updated description')
            ->assertJsonPath('data.status', 'disabled');
    }

    public function test_delete_project_removes_the_requested_project(): void
    {
        $this->seedProject(ApiTestData::projectPayload('status-page', 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->deleteJson('/api/v1/projects/status-page')
            ->assertNoContent();

        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/projects/status-page')
            ->assertNotFound();
    }
}
