<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Uplinkr\Handler\Project\Probes\RemoveProbeHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class DeleteProjectProbeAction
 * @package UplinkrApi\Actions\Api\Probes
 *
 * Removes a probe from a specific project and returns an API response payload.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class DeleteProjectProbeAction
{
    /**
     * Initializes the action with the probe catalog and probe removal handler.
     *
     * @param ProjectProbeCatalog $catalog The catalog used to locate the requested probe.
     * @param RemoveProbeHandler $removeProbeHandler The handler that removes the probe from the project.
     * @return void
     */
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
        private readonly RemoveProbeHandler  $removeProbeHandler,
    )
    {
    }

    /**
     * Deletes a probe from the given project if the probe exists.
     *
     * @param string $project The project slug or identifier.
     * @param string $probeId The API-specific probe identifier.
     * @return array The normalized response payload for the delete operation.
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

        $this->removeProbeHandler->handle([
            'project' => $project,
            'url' => (string)$probe['url'],
        ]);

        return [
            'status' => 204,
            'data' => null,
        ];
    }
}
