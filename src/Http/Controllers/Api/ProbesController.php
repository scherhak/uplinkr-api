<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UplinkrApi\Actions\Api\Probes\CreateProbeAction;
use UplinkrApi\Actions\Api\Probes\ListProbesAction;

class ProbesController
{
    public function index(Request $request, ListProbesAction $action): JsonResponse
    {
        return response()->json($action->execute($request));
    }

    public function store(Request $request, CreateProbeAction $action): JsonResponse
    {
        $result = $action->execute($request);
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        return response()->json($result, $status);
    }
}
