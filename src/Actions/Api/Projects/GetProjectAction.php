<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Uplinkr\Handler\Project\ListHandler;

/**
 * Class GetProjectAction
 * @package UplinkrApi\Actions\Api\Projects
 *
 * Resolves and returns a single project resource from the underlying Uplinkr storage.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class GetProjectAction
{
    /**
     * Initializes the action with the project list handler dependency.
     *
     * @param ListHandler $listHandler The handler used to inspect stored projects.
     * @return void
     */
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

    /**
     * Returns the requested project or a normalized not-found response payload.
     *
     * @param string $project The project slug or identifier.
     * @return array The normalized response payload containing the project resource.
     */
    public function execute(string $project): array
    {
        foreach ($this->listHandler->allWithDetails() as $item) {
            if ((string) ($item['project'] ?? '') === $project) {
                return [
                    'data' => $item,
                ];
            }
        }

        return [
            'status' => 404,
            'data' => null,
            'meta' => [
                'message' => 'Project not found.',
            ],
        ];
    }
}
