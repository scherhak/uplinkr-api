<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class GetProjectProbeAction
 * @package UplinkrApi\Actions\Api\Probes
 *
 * Resolves and returns a single probe for a specific project.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class GetProjectProbeAction
{
    /**
     * Initializes the action with the probe catalog dependency.
     *
     * @param ProjectProbeCatalog $catalog The catalog used to resolve probes for projects.
     * @return void
     */
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
    ) {
    }

    /**
     * Returns a single probe for the given project or a not-found payload.
     *
     * @param string $project The project slug or identifier.
     * @param string $probeId The API-specific probe identifier.
     * @return array The normalized response payload containing the probe or an error message.
     */
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
