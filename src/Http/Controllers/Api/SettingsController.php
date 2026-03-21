<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Settings\GetIamAliveSettingsAction;
use UplinkrApi\Actions\Api\Settings\UpdateIamAliveSettingsAction;

/**
 * Class SettingsController
 * @package UplinkrApi\Http\Controllers\Api
 *
 * Handles HTTP endpoints for global Uplinkr settings resources.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
#[Group(name: 'Settings', description: 'Global Uplinkr settings endpoints')]
class SettingsController
{
    #[Endpoint(operationId: 'getIamAliveSettings')]
    #[Response(status: 200, description: "Current I'm alive settings returned successfully")]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    /**
     * Handles an HTTP request to return the current I-am-alive settings.
     *
     * @param GetIamAliveSettingsAction $action The action that reads the global settings.
     * @return JsonResponse The JSON response containing the current settings.
     */
    public function showIamAlive(GetIamAliveSettingsAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    #[Endpoint(operationId: 'updateIamAliveSettings')]
    #[Response(status: 200, description: "I'm alive settings updated successfully")]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    #[Response(status: 422, description: 'Validation failed')]
    /**
     * Handles an HTTP request to update the current I-am-alive settings.
     *
     * @param Request $request The incoming HTTP request containing settings fields.
     * @param UpdateIamAliveSettingsAction $action The action that validates and updates the settings.
     * @return JsonResponse The JSON response describing the update result.
     */
    public function updateIamAlive(Request $request, UpdateIamAliveSettingsAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    /**
     * Converts a normalized action payload into a JSON HTTP response.
     *
     * @param array $result The normalized payload returned by an action class.
     * @return JsonResponse The final JSON response sent to the client.
     */
    private function respond(array $result): JsonResponse
    {
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
