<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Alerts;

use Illuminate\Support\Arr;
use JsonException;
use Uplinkr\Interfaces\ProjectStorageInterface;

/**
 * Reads and normalizes the alert settings for a single Uplinkr project.
 *
 * Legacy projects without certificate fields receive the stable v0.4.0
 * defaults in the response without changing their persisted settings.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class GetProjectAlertSettingsAction
{
    /**
     * Initializes the action with the project storage dependency.
     *
     * @param ProjectStorageInterface $projectStorage Storage used to resolve project settings.
     */
    public function __construct(
        private readonly ProjectStorageInterface $projectStorage,
    )
    {
    }

    /**
     * Returns normalized alert settings or a not-found response.
     *
     * @param string $project Project slug or identifier from the route.
     * @return array Normalized action response containing the alert resource.
     * @throws JsonException
     */
    public function execute(string $project): array
    {
        $projectData = $this->projectStorage->findProject($project);
        if ($projectData === null) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        return [
            'data' => $this->normalize(Arr::get($projectData, 'alerts.0', [])),
        ];
    }

    /**
     * Converts persisted settings into the stable public alert resource.
     *
     * @param array $alert Persisted first alert configuration, if present.
     * @return array Fully populated alert settings with deterministic list values.
     */
    private function normalize(array $alert): array
    {
        $warningDays = array_values(array_unique(array_map(
            'intval',
            Arr::get($alert, 'certificate_expiration_warning_days', [30, 14, 7, 3, 1]),
        )));
        rsort($warningDays, SORT_NUMERIC);

        return [
            'enabled' => (bool) Arr::get($alert, 'enabled', true),
            'trigger_after_failures' => (int) Arr::get($alert, 'trigger_after_failures', 3),
            'cooldown_minutes' => (int) Arr::get($alert, 'cooldown_minutes', 30),
            'latency_threshold_ms' => (int) Arr::get($alert, 'latency_threshold_ms', 1500),
            'trigger_after_slow' => (int) Arr::get($alert, 'trigger_after_slow', 3),
            'certificate_expiration_enabled' => (bool) Arr::get($alert, 'certificate_expiration_enabled', false),
            'certificate_expiration_warning_days' => $warningDays,
            'channels' => array_values(array_unique(Arr::get($alert, 'channels', ['mail']))),
        ];
    }
}
