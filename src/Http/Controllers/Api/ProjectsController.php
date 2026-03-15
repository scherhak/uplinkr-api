<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use UplinkrApi\Actions\Api\Projects\CreateProjectAction;
use UplinkrApi\Actions\Api\Projects\DeleteProjectAction;
use UplinkrApi\Actions\Api\Projects\GetProjectAction;
use UplinkrApi\Actions\Api\Projects\ListProjectsAction;
use UplinkrApi\Actions\Api\Projects\UpdateProjectAction;

class ProjectsController
{
    /**
     * @OA\Get(
     *     path="/api/v1/projects",
     *     operationId="listProjects",
     *     tags={"Projects"},
     *     summary="List all projects",
     *     description="Returns the current project list including probes and state summary.",
     *     security={{"uplinkrApiKey":{}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="query",
     *         description="Optional exact project filter",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project list returned successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Missing or invalid API key"
     *     )
     * )
     */
    public function index(Request $request, ListProjectsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    public function show(string $project, GetProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project));
    }

    public function update(string $project, Request $request, UpdateProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $request));
    }

    public function destroy(string $project, DeleteProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project));
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
