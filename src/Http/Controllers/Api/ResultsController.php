<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Results\CreateResultAction;
use UplinkrApi\Actions\Api\Results\GetResultAction;
use UplinkrApi\Actions\Api\Results\ListResultsAction;

/**
 * Class ResultsController
 * @package UplinkrApi\Http\Controllers\Api
 *
 * Handles HTTP endpoints for probe result resources.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ResultsController
{
    /**
     * Handles an HTTP request to list probe results.
     *
     * @param Request $request The incoming HTTP request containing optional result filters.
     * @param ListResultsAction $action The action that resolves the result collection.
     * @return JsonResponse The JSON response containing the requested results.
     */
    public function index(Request $request, ListResultsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    /**
     * Handles an HTTP request to create or trigger a probe result run.
     *
     * @param Request $request The incoming HTTP request containing the execution payload.
     * @param CreateResultAction $action The action that executes or queues the probe run.
     * @return JsonResponse The JSON response describing the execution result.
     */
    public function store(Request $request, CreateResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    /**
     * Handles an HTTP request to return a single result resource.
     *
     * @param string $resultId The API-specific result identifier from the route.
     * @param GetResultAction $action The action that resolves the requested result.
     * @return JsonResponse The JSON response containing the result resource.
     */
    public function show(string $resultId, GetResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($resultId));
    }

    /**
     * Handles an HTTP request to list results for a single project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param Request $request The incoming HTTP request containing optional result filters.
     * @param ListResultsAction $action The action that resolves the result collection.
     * @return JsonResponse The JSON response containing the project-scoped result list.
     */
    public function projectIndex(string $project, Request $request, ListResultsAction $action): JsonResponse
    {
        return response()->json($action->execute($request, $project));
    }

    /**
     * Handles an HTTP request to create or trigger a probe result run for a project.
     *
     * @param string $project The project slug or identifier from the route.
     * @param Request $request The incoming HTTP request containing the execution payload.
     * @param CreateResultAction $action The action that executes or queues the probe run.
     * @return JsonResponse The JSON response describing the execution result.
     */
    public function projectStore(string $project, Request $request, CreateResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($request, $project));
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
