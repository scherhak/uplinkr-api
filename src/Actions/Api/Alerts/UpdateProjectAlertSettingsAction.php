<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Alerts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Project\Alerts\AlertHandler;

/**
 * Validates and applies partial updates to project alert settings.
 *
 * The action delegates persistence and default preservation to Uplinkr's
 * public AlertHandler so API and CLI updates share identical behavior.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class UpdateProjectAlertSettingsAction
{
    /**
     * Initializes the action with the Uplinkr alert handler and read action.
     *
     * @param AlertHandler $alertHandler Handler that persists project alert settings.
     * @param GetProjectAlertSettingsAction $getAlertSettings Action used to return the updated resource.
     */
    public function __construct(
        private readonly AlertHandler $alertHandler,
        private readonly GetProjectAlertSettingsAction $getAlertSettings,
    )
    {
    }

    /**
     * Validates a partial alert payload and persists the supplied fields.
     *
     * @param string $project Project slug or identifier from the route.
     * @param Request $request Incoming request containing one or more alert fields.
     * @return array Normalized action response containing the updated alert resource.
     * @throws JsonException
     */
    public function execute(string $project, Request $request): array
    {
        $data = Validator::make($request->all(), [
            'enabled' => ['sometimes', 'boolean'],
            'trigger_after_failures' => ['sometimes', 'integer', 'min:1'],
            'cooldown_minutes' => ['sometimes', 'integer', 'min:1'],
            'latency_threshold_ms' => ['sometimes', 'integer', 'min:1'],
            'trigger_after_slow' => ['sometimes', 'integer', 'min:1'],
            'certificate_expiration_enabled' => ['sometimes', 'boolean'],
            'certificate_expiration_warning_days' => ['sometimes', 'array', 'min:1'],
            'certificate_expiration_warning_days.*' => ['integer', 'min:1', 'distinct'],
            'channels' => ['sometimes', 'array', 'min:1'],
            'channels.*' => ['string', 'in:mail,log,webhook', 'distinct'],
        ])->validate();

        if ($data === []) {
            return [
                'status' => 422,
                'data' => null,
                'meta' => [
                    'message' => 'At least one alert settings field must be provided.',
                ],
            ];
        }

        if (array_key_exists('certificate_expiration_warning_days', $data)) {
            rsort($data['certificate_expiration_warning_days'], SORT_NUMERIC);
        }

        $updated = $this->alertHandler->handle([
            'project' => $project,
            ...$data,
        ]);

        if (!$updated) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        return $this->getAlertSettings->execute($project);
    }
}
