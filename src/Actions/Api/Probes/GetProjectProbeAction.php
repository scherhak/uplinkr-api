<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use UplinkrApi\Support\Api\ProjectProbeCatalog;

class GetProjectProbeAction
{
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
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

        return [
            'data' => $probe,
        ];
    }
}
