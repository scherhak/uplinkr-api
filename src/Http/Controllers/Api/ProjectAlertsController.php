<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use UplinkrApi\Actions\Api\Alerts\GetProjectAlertSettingsAction;
use UplinkrApi\Actions\Api\Alerts\UpdateProjectAlertSettingsAction;

/**
 * Exposes project-scoped alert configuration through versioned REST endpoints.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
#[Group(name: 'Project Alerts', description: 'Project alert settings endpoints')]
class ProjectAlertsController
{
    #[Endpoint(operationId: 'getProjectAlertSettings')]
    #[Response(status: 200, description: 'Project alert settings returned successfully')]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    #[Response(status: 404, description: 'Project not found')]
    /**
     * Returns normalized alert settings for a project.
     *
     * @param string $project Project slug or identifier from the route.
     * @param GetProjectAlertSettingsAction $action Action that reads and normalizes alert settings.
     * @return JsonResponse JSON response containing the project alert resource.
     * @throws JsonException
     */
    public function show(string $project, GetProjectAlertSettingsAction $action): JsonResponse
    {
        return $this->respond($action->execute($project));
    }

    #[Endpoint(operationId: 'updateProjectAlertSettings')]
    #[Response(status: 200, description: 'Project alert settings updated successfully')]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    #[Response(status: 404, description: 'Project not found')]
    #[Response(status: 422, description: 'Validation failed')]
    /**
     * Applies a partial update and returns the resulting alert settings.
     *
     * @param string $project Project slug or identifier from the route.
     * @param Request $request Incoming request containing partial alert settings.
     * @param UpdateProjectAlertSettingsAction $action Action that validates and persists the update.
     * @return JsonResponse JSON response containing the updated project alert resource.
     * @throws JsonException
     */
    public function update(
        string $project,
        Request $request,
        UpdateProjectAlertSettingsAction $action,
    ): JsonResponse {
        return $this->respond($action->execute($project, $request));
    }

    /**
     * Converts a normalized action payload into an HTTP JSON response.
     *
     * @param array $result Normalized payload returned by an alert action.
     * @return JsonResponse Final response sent to the API client.
     */
    private function respond(array $result): JsonResponse
    {
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
