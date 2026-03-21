<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use UplinkrApi\Support\Api\ProjectProbeCatalog;
use UplinkrApi\Tests\Support\ApiTestData;
use UplinkrApi\Tests\TestCase;

class ProbesApiTest extends TestCase
{
    public function test_get_probes_returns_seeded_probes(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $firstUrl = sprintf('https://example.com/%s-health', $project);
        $secondUrl = sprintf('https://example.com/%s-ping', $project);

        $this->seedProject(ApiTestData::projectPayload($project));
        $this->seedProbe(ApiTestData::probePayload($firstUrl, 'GET', $project));
        $this->seedProbe(ApiTestData::probePayload($secondUrl, 'POST', $project));

        $response = $this->withHeaders($this->apiHeaders())
            ->getJson('/api/v1/probes')
            ->assertOk();

        $response->assertJsonFragment(['url' => $firstUrl]);
        $response->assertJsonFragment(['url' => $secondUrl]);
    }

    public function test_post_probes_creates_a_probe_for_a_project_from_payload(): void
    {
        $project = $this->uniqueProjectName('status-page');

        $this->withHeaders($this->apiHeaders())
            ->postJson('/api/v1/projects', ApiTestData::projectPayload($project))
            ->assertCreated();

        $this->withHeaders($this->apiHeaders())
            ->postJson('/api/v1/probes', ApiTestData::probePayload('https://example.com/health', 'GET', $project))
            ->assertCreated()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.url', 'https://example.com/health')
            ->assertJsonPath('meta.created', true);
    }

    public function test_get_project_probes_returns_probes_for_the_requested_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $otherProject = $this->uniqueProjectName('other-project');
        $this->seedProject(ApiTestData::projectPayload($project));
        $this->seedProject(ApiTestData::projectPayload($otherProject));
        $this->seedProbe(ApiTestData::probePayload('https://example.com/health', 'GET', $project));
        $this->seedProbe(ApiTestData::probePayload('https://example.org/health', 'GET', $otherProject));

        $response = $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s/probes', $project))
            ->assertOk()
            ->assertJsonPath('data.0.project', $project);

        $response->assertJsonMissing(['project' => $otherProject]);
    }

    public function test_post_project_probe_creates_a_probe_under_the_requested_project(): void
    {
        $project = $this->uniqueProjectName('status-page');
        $this->seedProject(ApiTestData::projectPayload($project));

        $this->withHeaders($this->apiHeaders())
            ->postJson(sprintf('/api/v1/projects/%s/probes', $project), ApiTestData::probePayload())
            ->assertCreated()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.url', 'https://example.com/health')
            ->assertJsonPath('meta.created', true);
    }

    public function test_get_project_probe_returns_the_requested_probe(): void
    {
        $project = 'status-page';
        $url = 'https://example.com/health';
        $method = 'GET';

        $this->seedProject(ApiTestData::projectPayload($project));
        $this->seedProbe(ApiTestData::probePayload($url, $method, $project));

        $probeId = $this->app->make(ProjectProbeCatalog::class)->buildProbeId($project, [
            'url' => $url,
            'method' => $method,
        ]);

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s/probes/%s', $project, $probeId))
            ->assertOk()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.url', $url)
            ->assertJsonPath('data.probe_id', $probeId);
    }

    public function test_patch_project_probe_updates_the_requested_probe(): void
    {
        $project = 'status-page';
        $originalUrl = 'https://example.com/health';
        $updatedUrl = 'https://example.com/ready';

        $this->seedProject(ApiTestData::projectPayload($project));
        $this->seedProbe(ApiTestData::probePayload($originalUrl, 'GET', $project));

        $probeId = $this->app->make(ProjectProbeCatalog::class)->buildProbeId($project, [
            'url' => $originalUrl,
            'method' => 'GET',
        ]);

        $this->withHeaders($this->apiHeaders())
            ->patchJson(sprintf('/api/v1/projects/%s/probes/%s', $project, $probeId), [
                'url' => $updatedUrl,
                'method' => 'POST',
                'headers' => ['Accept' => 'application/xml'],
                'body' => '{"ready":true}',
            ])
            ->assertOk()
            ->assertJsonPath('data.project', $project)
            ->assertJsonPath('data.url', $updatedUrl);

        $updatedProbeId = $this->app->make(ProjectProbeCatalog::class)->buildProbeId($project, [
            'url' => $updatedUrl,
            'method' => 'POST',
        ]);

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s/probes/%s', $project, $updatedProbeId))
            ->assertOk()
            ->assertJsonPath('data.url', $updatedUrl)
            ->assertJsonPath('data.method', 'POST');
    }

    public function test_delete_project_probe_removes_the_requested_probe(): void
    {
        $project = 'status-page';
        $url = 'https://example.com/health';

        $this->seedProject(ApiTestData::projectPayload($project));
        $this->seedProbe(ApiTestData::probePayload($url, 'GET', $project));

        $probeId = $this->app->make(ProjectProbeCatalog::class)->buildProbeId($project, [
            'url' => $url,
            'method' => 'GET',
        ]);

        $this->withHeaders($this->apiHeaders())
            ->deleteJson(sprintf('/api/v1/projects/%s/probes/%s', $project, $probeId))
            ->assertNoContent();

        $this->withHeaders($this->apiHeaders())
            ->getJson(sprintf('/api/v1/projects/%s/probes/%s', $project, $probeId))
            ->assertNotFound();
    }
}
