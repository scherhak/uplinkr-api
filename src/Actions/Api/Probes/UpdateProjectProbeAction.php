<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use Uplinkr\Handler\Project\Probes\RemoveProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class UpdateProjectProbeAction
 * @package UplinkrApi\Actions\Api\Probes
 *
 * Updates an existing project probe by removing the current definition and
 * writing a new normalized probe payload.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class UpdateProjectProbeAction
{
    /**
     * Initializes the action with the required catalog and probe handlers.
     *
     * @param ProjectProbeCatalog $catalog The catalog used to locate the existing probe.
     * @param RemoveProbeHandler $removeProbeHandler The handler that removes the old probe definition.
     * @param AddProbeHandler $addProbeHandler The handler that persists the updated probe definition.
     * @return void
     */
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
        private readonly RemoveProbeHandler $removeProbeHandler,
        private readonly AddProbeHandler $addProbeHandler,
    ) {
    }

    /**
     * Validates and applies a partial update to a probe in the specified project.
     *
     * @param string $project The project slug or identifier.
     * @param string $probeId The API-specific probe identifier.
     * @param Request $request The incoming HTTP request with updated probe attributes.
     * @return array The normalized response payload for the update operation.
     */
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
