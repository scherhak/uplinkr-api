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

#[Group(name: 'Settings', description: 'Global Uplinkr settings endpoints')]
class SettingsController
{
    #[Endpoint(operationId: 'getIamAliveSettings')]
    #[Response(status: 200, description: "Current I'm alive settings returned successfully")]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    public function showIamAlive(GetIamAliveSettingsAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    #[Endpoint(operationId: 'updateIamAliveSettings')]
    #[Response(status: 200, description: "I'm alive settings updated successfully")]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    #[Response(status: 422, description: 'Validation failed')]
    public function updateIamAlive(Request $request, UpdateIamAliveSettingsAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    private function respond(array $result): JsonResponse
    {
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
