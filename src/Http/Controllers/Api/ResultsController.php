<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Results\CreateResultAction;
use UplinkrApi\Actions\Api\Results\GetResultAction;
use UplinkrApi\Actions\Api\Results\ListResultsAction;

class ResultsController
{
    public function index(Request $request, ListResultsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($request));
    }

    public function show(string $resultId, GetResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($resultId));
    }

    public function projectIndex(string $project, Request $request, ListResultsAction $action): JsonResponse
    {
        return response()->json($action->execute($request, $project));
    }

    public function projectStore(string $project, Request $request, CreateResultAction $action): JsonResponse
    {
        return $this->respond($action->execute($request, $project));
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
