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
        $alphaProject = $this->uniqueProjectName('alpha-project');
        $betaProject = $this->uniqueProjectName('beta-project');

        $this->seedProject(ApiTestData::projectPayload($alphaProject, 'Alpha', 'Alpha description'));
        $this->seedProject(ApiTestData::projectPayload($betaProject, 'Beta', 'Beta description'));

        $response = $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/projects')
            ->assertOk();

        $response->assertJsonFragment(['project' => $alphaProject]);
        $response->assertJsonFragment(['project' => $betaProject]);
    }

    public function test_post_projects_creates_a_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $payload = ApiTestData::projectPayload($project, 'Status Page', 'Public status page monitoring');

        $this->withHeaders($this->apiHeaders())
            ->postJson('/api/v1/projects', $payload)
            ->assertCreated()
            ->assertJsonPath('data.project', $project);

        $this->assertTrue(Storage::disk('local')->exists(sprintf('%s/%s/settings.json', $this->storagePath(), $project)));
    }

    public function test_get_project_returns_the_requested_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $this->seedProject(ApiTestData::projectPayload($project, 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s', $project))
            ->assertOk()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.label', 'Status Page');
    }

    public function test_patch_project_updates_the_requested_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $this->seedProject(ApiTestData::projectPayload($project, 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->patchJson(sprintf('/api/v1/projects/%s', $project), [
                'label' => 'Updated Status Page',
                'description' => 'Updated description',
                'status' => 'disabled',
            ])
            ->assertOk()
            ->assertJsonPath('data.project', $project);

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s', $project))
            ->assertOk()
            ->assertJsonPath('data.label', 'Updated Status Page')
            ->assertJsonPath('data.description', 'Updated description')
            ->assertJsonPath('data.status', 'disabled');
    }

    public function test_delete_project_removes_the_requested_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $this->seedProject(ApiTestData::projectPayload($project, 'Status Page', 'Public status page monitoring'));

        $this->withHeaders($this->apiHeaders())
            ->deleteJson(sprintf('/api/v1/projects/%s', $project))
            ->assertOk()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.deleted', true)
            ->assertJsonPath('data.archived', true)
            ->assertJsonPath('meta.message', 'Project deleted successfully.');

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s', $project))
            ->assertNotFound();
    }
}
