<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Uplinkr\Handler\Project\ListHandler;
use Uplinkr\Handler\Project\Probes\AddProbeHandler;

class CreateProbeAction
{
    public function __construct(
        private readonly AddProbeHandler $addProbeHandler,
        private readonly ListHandler $listHandler,
    ) {
    }

    public function execute(Request $request): array
    {
        $data = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'project' => ['required', 'string'],
            'method' => ['nullable', 'string', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'headers' => ['nullable', 'array'],
            'headers.*' => ['string'],
            'body' => ['nullable'],
            'latency' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $projectName = (string) $data['project'];
        if (!$this->hasProject($projectName)) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        $url = (string) $data['url'];
        $alreadyExists = $this->hasProbe($projectName, $url);

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
            ],
            'meta' => [
                'created' => !$alreadyExists,
            ],
        ];
    }

    private function hasProject(string $projectName): bool
    {
        foreach ($this->listHandler->allWithDetails() as $project) {
            if ((string) ($project['project'] ?? '') === $projectName) {
                return true;
            }
        }

        return false;
    }

    private function hasProbe(string $projectName, string $url): bool
    {
        foreach ($this->listHandler->allWithDetails() as $project) {
            if ((string) ($project['project'] ?? '') !== $projectName) {
                continue;
            }

            foreach (($project['probes'] ?? []) as $probe) {
                if (!is_array($probe)) {
                    continue;
                }

                if ((string) ($probe['url'] ?? '') === $url) {
                    return true;
                }
            }
        }

        return false;
    }
}
