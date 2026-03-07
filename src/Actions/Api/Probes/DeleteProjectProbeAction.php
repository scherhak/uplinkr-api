<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Uplinkr\Handler\Project\Probes\RemoveProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

class DeleteProjectProbeAction
{
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
        private readonly RemoveProbeHandler $removeProbeHandler,
    ) {
    }

    public function execute(string $project, string $probeId): array
    {
        $probe = $this->catalog->findProbe($project, $probeId);
        if ($probe === null) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Probe not found.',
                ],
            ];
        }

        $this->removeProbeHandler->handle([
            'project' => $project,
            'url' => (string) $probe['url'],
        ]);

        return [
            'status' => 204,
            'data' => null,
        ];
    }
}
