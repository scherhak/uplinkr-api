<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use UplinkrApi\Actions\Api\Projects\CreateProjectAction;
use UplinkrApi\Actions\Api\Projects\DeleteProjectAction;
use UplinkrApi\Actions\Api\Projects\GetProjectAction;
use UplinkrApi\Actions\Api\Projects\ListProjectsAction;
use UplinkrApi\Actions\Api\Projects\UpdateProjectAction;

/**
 * Class ProjectsController
 * @package UplinkrApi\Http\Controllers\Api
 *
 * Handles REST-style HTTP endpoints for Uplinkr project resources.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
#[Group(name: 'Projects', description: 'Project management endpoints')]
class ProjectsController
{
    #[Endpoint(operationId: 'listProjects')]
    #[QueryParameter('project', description: 'Optional exact project filter', required: false, type: 'string')]
    #[Response(status: 200, description: 'Project list returned successfully')]
    #[Response(status: 401, description: 'Missing or invalid API key')]
    /**
     * Handles an HTTP request to list projects.
     *
     * @param Request $request The incoming HTTP request containing optional query filters.
     * @param ListProjectsAction $action The action that resolves the project collection.
     * @return JsonResponse The JSON response containing the requested projects.
     */
    public function index(Request $request, ListProjectsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    /**
     * Handles an HTTP request to create a new project.
     *
     * @param Request $request The incoming HTTP request containing the project payload.
     * @param CreateProjectAction $action The action that validates and creates the project.
     * @return JsonResponse The JSON response describing the create result.
     * @throws JsonException
     */
    public function store(Request $request, CreateProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    /**
     * Handles an HTTP request to return a single project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param GetProjectAction $action The action that resolves the project resource.
     * @return JsonResponse The JSON response containing the requested project.
     */
    public function show(string $project, GetProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project));
    }

    /**
     * Handles an HTTP request to partially update a project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param Request $request The incoming HTTP request containing partial update data.
     * @param UpdateProjectAction $action The action that applies the project updates.
     * @return JsonResponse The JSON response describing the update result.
     * @throws JsonException
     */
    public function update(string $project, Request $request, UpdateProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project, $request));
    }

    /**
     * Handles an HTTP request to delete a project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param DeleteProjectAction $action The action that archives and deletes the project.
     * @return JsonResponse The JSON response describing the delete result.
     */
    public function destroy(string $project, DeleteProjectAction $action): JsonResponse
    {
        return $this->respond($action->execute($project));
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
