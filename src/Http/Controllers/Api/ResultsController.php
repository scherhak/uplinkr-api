<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Results\CreateResultAction;
use UplinkrApi\Actions\Api\Results\ListResultsAction;

class ResultsController
{
    public function index(Request $request, ListResultsAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateResultAction $action): JsonResponse
    {
        $result = $action->execute($request);
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
