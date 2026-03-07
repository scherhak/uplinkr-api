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

class ProbesController
{
    public function index(Request $request, ListProbesAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    public function projectIndex(string $project, Request $request, ListProbesAction $action): JsonResponse
    {
        return response()->json($action->execute($request, $project));
    }

    public function projectStore(string $project, Request $request, CreateProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($request, $project));
    }

    public function show(string $project, string $probeId, GetProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId));
    }

    public function update(string $project, string $probeId, Request $request, UpdateProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId, $request));
    }

    public function destroy(string $project, string $probeId, DeleteProjectProbeAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $probeId));
    }

    private function respond(array $result): JsonResponse
    {
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        if ($status === 204) {
            return response()->json(null, 204);
        }

        return response()->json($result, $status);
    }
}
