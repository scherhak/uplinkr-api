<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Projects\CreateProjectAction;
use UplinkrApi\Actions\Api\Projects\ListProjectsAction;

class ProjectsController
{
    public function index(Request $request, ListProjectsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateProjectAction $action): JsonResponse
    {
        $result = $action->execute($request);
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
