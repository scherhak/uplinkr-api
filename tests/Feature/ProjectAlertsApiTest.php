<?php

declare(strict_types=1);

namespace UplinkrApi\Tests\Feature;

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Illuminate\Support\Facades\Storage;
use JsonException;
use UplinkrApi\Tests\Support\ApiTestData;
use UplinkrApi\Tests\TestCase;

class ProjectAlertsApiTest extends TestCase
{
    public function test_get_alerts_normalizes_legacy_project_defaults(): void
    {
        $project = $this->createProject();

        $this->withHeaders($this->apiHeaders())
            ->getJson($this->alertsUrl($project))
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'enabled' => true,
                    'trigger_after_failures' => 3,
                    'cooldown_minutes' => 30,
                    'latency_threshold_ms' => 1500,
                    'trigger_after_slow' => 3,
                    'certificate_expiration_enabled' => false,
                    'certificate_expiration_warning_days' => [30, 14, 7, 3, 1],
                    'channels' => ['mail'],
                ],
            ]);
    }

    public function test_get_alerts_returns_not_found_for_unknown_project(): void
    {
        $this->withHeaders($this->apiHeaders())
            ->getJson($this->alertsUrl('missing-project'))
            ->assertNotFound()
            ->assertJsonPath('meta.message', 'Project not found.');
    }

    public function test_patch_alerts_updates_and_normalizes_all_fields(): void
    {
        $project = $this->createProject();

        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl($project), [
                'enabled' => false,
                'trigger_after_failures' => 5,
                'cooldown_minutes' => 45,
                'latency_threshold_ms' => 2500,
                'trigger_after_slow' => 4,
                'certificate_expiration_enabled' => true,
                'certificate_expiration_warning_days' => [1, 30, 7, 14, 3],
                'channels' => ['webhook', 'log'],
            ])
            ->assertOk()
            ->assertJsonPath('data.enabled', false)
            ->assertJsonPath('data.certificate_expiration_enabled', true)
            ->assertJsonPath('data.certificate_expiration_warning_days', [30, 14, 7, 3, 1])
            ->assertJsonPath('data.channels', ['webhook', 'log']);

        $settings = $this->readProjectSettings($project);
        $this->assertSame([30, 14, 7, 3, 1], $settings['alerts'][0]['certificate_expiration_warning_days']);
    }

    public function test_patch_alerts_preserves_fields_omitted_from_partial_update(): void
    {
        $project = $this->createProject();

        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl($project), [
                'trigger_after_failures' => 8,
                'channels' => ['mail', 'log'],
            ])
            ->assertOk();

        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl($project), [
                'certificate_expiration_enabled' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.trigger_after_failures', 8)
            ->assertJsonPath('data.channels', ['mail', 'log'])
            ->assertJsonPath('data.certificate_expiration_enabled', true);
    }

    public function test_patch_alerts_rejects_empty_payload(): void
    {
        $project = $this->createProject();

        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl($project), [])
            ->assertUnprocessable()
            ->assertJsonPath('meta.message', 'At least one alert settings field must be provided.');
    }

    public function test_patch_alerts_rejects_invalid_thresholds_and_channels(): void
    {
        $project = $this->createProject();

        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl($project), [
                'certificate_expiration_warning_days' => [14, 14, 0],
                'channels' => ['mail', 'sms'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'certificate_expiration_warning_days.1',
                'certificate_expiration_warning_days.2',
                'channels.1',
            ]);
    }

    public function test_patch_alerts_returns_not_found_for_unknown_project(): void
    {
        $this->withHeaders($this->apiHeaders())
            ->patchJson($this->alertsUrl('missing-project'), ['enabled' => false])
            ->assertNotFound()
            ->assertJsonPath('meta.message', 'Project not found.');
    }

    public function test_alert_endpoints_require_authentication(): void
    {
        $project = $this->createProject();

        $this->getJson($this->alertsUrl($project))->assertUnauthorized();
        $this->patchJson($this->alertsUrl($project), ['enabled' => false])->assertUnauthorized();
    }

    public function test_openapi_document_contains_project_alert_operations(): void
    {
        $response = $this->withoutMiddleware(RestrictedDocsAccess::class)
            ->getJson('/api/docs/openapi')
            ->assertOk();

        $response->assertJsonPath(
            'paths./v1/projects/{project}/alerts.get.operationId',
            'getProjectAlertSettings',
        );
        $response->assertJsonPath(
            'paths./v1/projects/{project}/alerts.patch.operationId',
            'updateProjectAlertSettings',
        );
    }

    private function createProject(): string
    {
        $project = $this->uniqueProjectName('alert-project');
        $this->seedProject(ApiTestData::projectPayload($project));

        return $project;
    }

    private function alertsUrl(string $project): string
    {
        return sprintf('/api/v1/projects/%s/alerts', $project);
    }

    /**
     * @throws JsonException
     */
    private function readProjectSettings(string $project): array
    {
        $contents = Storage::disk('local')->get(sprintf(
            '%s/%s/settings.json',
            $this->storagePath(),
            $project,
        ));

        return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }
}
