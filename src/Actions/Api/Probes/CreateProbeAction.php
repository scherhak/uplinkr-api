<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class CreateProbeAction
 * @package UplinkrApi\Actions\Api\Probes
 *
 * Creates a probe either through the global probes endpoint or as a nested
 * project resource and returns a normalized API response payload.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class CreateProbeAction
{
    /**
     * Initializes the action with the required probe write handler and catalog.
     *
     * @param AddProbeHandler $addProbeHandler The handler that persists the probe in the base package.
     * @param ProjectProbeCatalog $catalog The catalog used to resolve existing projects and probes.
     * @return void
     */
    public function __construct(
        private readonly AddProbeHandler     $addProbeHandler,
        private readonly ProjectProbeCatalog $catalog,
    )
    {
    }

    /**
     * Validates the incoming payload, resolves the target project, stores the probe,
     * and returns a REST-style response structure.
     *
     * @param Request $request The incoming HTTP request containing the probe payload.
     * @param string|null $project An optional project name from a nested route parameter.
     * @return array The normalized response payload including status, data, and metadata.
     */
    public function execute(Request $request, ?string $project = null): array
    {
        $data = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'project' => ['nullable', 'string'],
            'method' => ['nullable', 'string', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'headers' => ['nullable', 'array'],
            'headers.*' => ['string'],
            'body' => ['nullable'],
            'latency' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $projectName = $project ?? (isset($data['project']) ? (string)$data['project'] : '');
        if ($projectName === '' || !$this->catalog->hasProject($projectName)) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        $url = (string)$data['url'];
        $existing = $this->catalog->listProbes($projectName);
        $alreadyExists = false;

        foreach ($existing as $probeItem) {
            if ((string)($probeItem['url'] ?? '') === $url) {
                $alreadyExists = true;
                break;
            }
        }

        $this->addProbeHandler->handle([
            'url' => $url,
            'project' => $projectName,
            'method' => (string)($data['method'] ?? 'GET'),
            'headers' => $data['headers'] ?? [],
            'body' => $data['body'] ?? null,
            'latency' => $data['latency'] ?? null,
        ]);

        return [
            'status' => $alreadyExists ? 200 : 201,
            'data' => [
                'project' => $projectName,
                'url' => $url,
                'probe_id' => $this->catalog->buildProbeId($projectName, [
                    'method' => (string)($data['method'] ?? 'GET'),
                    'url' => $url,
                ]),
            ],
            'meta' => [
                'created' => !$alreadyExists,
            ],
        ];
    }
}
