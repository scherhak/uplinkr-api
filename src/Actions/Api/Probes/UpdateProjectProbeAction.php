<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use Uplinkr\Handler\Project\Probes\RemoveProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

class UpdateProjectProbeAction
{
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
        private readonly RemoveProbeHandler $removeProbeHandler,
        private readonly AddProbeHandler $addProbeHandler,
    ) {
    }

    public function execute(string $project, string $probeId, Request $request): array
    {
        $existing = $this->catalog->findProbe($project, $probeId);
        if ($existing === null) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Probe not found.',
                ],
            ];
        }

        $data = Validator::make($request->all(), [
            'url' => ['sometimes', 'url'],
            'method' => ['sometimes', 'string', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'headers' => ['sometimes', 'array'],
            'headers.*' => ['string'],
            'body' => ['sometimes', 'nullable'],
            'latency' => ['sometimes', 'integer', 'min:1'],
        ])->validate();

        $updatedProbe = [
            'url' => (string) ($data['url'] ?? $existing['url']),
            'project' => $project,
            'method' => (string) ($data['method'] ?? $existing['method'] ?? 'GET'),
            'headers' => $data['headers'] ?? ($existing['headers'] ?? []),
            'body' => array_key_exists('body', $data) ? $data['body'] : ($existing['body'] ?? null),
            'latency' => array_key_exists('latency', $data) ? $data['latency'] : ($existing['latency'] ?? null),
        ];

        $this->removeProbeHandler->handle([
            'project' => $project,
            'url' => (string) $existing['url'],
        ]);

        $this->addProbeHandler->handle($updatedProbe);

        return [
            'data' => [
                'project' => $project,
                'url' => $updatedProbe['url'],
                'probe_id' => $this->catalog->buildProbeId($project, $updatedProbe),
            ],
        ];
    }
}
