<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use Uplinkr\Handler\Project\ListHandler;

class ListProbesAction
{
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

    public function execute(Request $request): array
    {
        $projectFilter = trim((string) $request->query('project', ''));
        $probes = [];

        foreach ($this->listHandler->allWithDetails() as $project) {
            $projectName = (string) ($project['project'] ?? '');
            if ($projectFilter !== '' && $projectName !== $projectFilter) {
                continue;
            }

            foreach (($project['probes'] ?? []) as $probe) {
                if (!is_array($probe)) {
                    continue;
                }

                $probe['project'] = $probe['project'] ?? $projectName;
                $probes[] = $probe;
            }
        }

        return [
            'data' => $probes,
            'meta' => [
                'count' => count($probes),
            ],
        ];
    }
}
