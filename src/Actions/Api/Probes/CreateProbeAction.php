<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

class CreateProbeAction
{
    public function __construct(
        private readonly AddProbeHandler $addProbeHandler,
        private readonly ProjectProbeCatalog $catalog,
    ) {
    }

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

        $projectName = $project ?? (isset($data['project']) ? (string) $data['project'] : '');
        if ($projectName === '' || !$this->catalog->hasProject($projectName)) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        $url = (string) $data['url'];
        $existing = $this->catalog->listProbes($projectName);
        $alreadyExists = false;

        foreach ($existing as $probeItem) {
            if ((string) ($probeItem['url'] ?? '') === $url) {
                $alreadyExists = true;
                break;
            }
        }

        $this->addProbeHandler->handle([
            'url' => $url,
            'project' => $projectName,
            'method' => (string) ($data['method'] ?? 'GET'),
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
                    'method' => (string) ($data['method'] ?? 'GET'),
                    'url' => $url,
                ]),
            ],
            'meta' => [
                'created' => !$alreadyExists,
            ],
        ];
    }
}
