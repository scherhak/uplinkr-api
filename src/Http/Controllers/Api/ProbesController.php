<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Probes\CreateProbeAction;
use UplinkrApi\Actions\Api\Probes\DeleteProjectProbeAction;
use UplinkrApi\Actions\Api\Probes\GetProjectProbeAction;
use UplinkrApi\Actions\Api\Probes\ListProbesAction;
use UplinkrApi\Actions\Api\Probes\UpdateProjectProbeAction;

/**
 * Class ProbesController
 * @package UplinkrApi\Http\Controllers\Api
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ProbesController
{
    /**
     * Handles an HTTP request to list probes and returns the result as a JSON response.
     *
     * @param Request $request The incoming HTTP request containing any necessary data or parameters.
     * @param ListProbesAction $action The action that executes the logic for listing probes.
     * @return JsonResponse The JSON response containing the result of the executed action.
     */
    public function index(Request $request, ListProbesAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    /**
     * Handles an HTTP request to create a probe through the global probes endpoint.
     *
     * @param Request $request The incoming HTTP request containing the probe payload.
     * @param CreateProbeAction $action The action that validates and stores the probe.
     * @return JsonResponse The JSON response describing the create result.
     */
    public function store(Request $request, CreateProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    /**
     * Handles an HTTP request to list probes for a single project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param Request $request The incoming HTTP request containing optional query filters.
     * @param ListProbesAction $action The action that executes the probe listing logic.
     * @return JsonResponse The JSON response containing the project-scoped probe list.
     */
    public function projectIndex(string $project, Request $request, ListProbesAction $action): JsonResponse
    {
        return response()->json($action->execute($request, $project));
    }

    /**
     * Handles an HTTP request to create a probe under a specific project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param Request $request The incoming HTTP request containing the probe payload.
     * @param CreateProbeAction $action The action that validates and stores the probe.
     * @return JsonResponse The JSON response describing the create result.
     */
    public function projectStore(string $project, Request $request, CreateProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($request, $project));
    }

    /**
     * Handles an HTTP request to return a single project probe.
     *
     * @param string $project The project slug or identifier from the route.
     * @param string $probeId The API-specific probe identifier from the route.
     * @param GetProjectProbeAction $action The action that resolves the requested probe.
     * @return JsonResponse The JSON response containing the probe resource.
     */
    public function show(string $project, string $probeId, GetProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId));
    }

    /**
     * Handles an HTTP request to update a single project probe.
     *
     * @param string $project The project slug or identifier from the route.
     * @param string $probeId The API-specific probe identifier from the route.
     * @param Request $request The incoming HTTP request with partial update data.
     * @param UpdateProjectProbeAction $action The action that applies the requested updates.
     * @return JsonResponse The JSON response describing the update result.
     */
    public function update(string $project, string $probeId, Request $request, UpdateProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId, $request));
    }

    /**
     * Handles an HTTP request to delete a single probe from a project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param string $probeId The API-specific probe identifier from the route.
     * @param DeleteProjectProbeAction $action The action that deletes the requested probe.
     * @return JsonResponse The JSON response describing the delete result.
     */
    public function destroy(string $project, string $probeId, DeleteProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId));
    }

    /**
     * Converts a normalized action payload into a JSON HTTP response.
     *
     * @param array $result The normalized payload returned by an action class.
     * @return JsonResponse The final JSON response sent to the client.
     */
    private function respond(array $result): JsonResponse
    {
        $status = (int)($result['status'] ?? 200);
        unset($result['status']);

        if ($status === 204) {
            return response()->json(null, 204);
        }

        return response()->json($result, $status);
    }
}
