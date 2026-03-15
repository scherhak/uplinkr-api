<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Projects\CreateProjectAction;
use UplinkrApi\Actions\Api\Projects\DeleteProjectAction;
use UplinkrApi\Actions\Api\Projects\GetProjectAction;
use UplinkrApi\Actions\Api\Projects\ListProjectsAction;
use UplinkrApi\Actions\Api\Projects\UpdateProjectAction;

#[Group(name: 'Projects', description: 'Project management endpoints')]
class ProjectsController
{
    #[Endpoint(operationId: 'listProjects')]
    #[QueryParameter('project', description: 'Optional exact project filter', required: false, type: 'string')]
    #[Response(status: 200, description: 'Project list returned successfully')]
    #[Response(status: 401, description: 'Missing or invalid API key')]
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
