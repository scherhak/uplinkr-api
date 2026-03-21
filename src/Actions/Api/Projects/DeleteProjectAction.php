<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Uplinkr\Handler\Project\Archive\ArchiveHandler;

/**
 * Class DeleteProjectAction
 * @package UplinkrApi\Actions\Api\Projects
 *
 * Archives and deletes a project through the base package and returns a
 * structured API response for the delete operation.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class DeleteProjectAction
{
    /**
     * Initializes the action with the archive handler dependency.
     *
     * @param ArchiveHandler $archiveHandler The handler that archives and removes projects.
     * @return void
     */
    public function __construct(
        private readonly ArchiveHandler $archiveHandler,
    )
    {
    }

    /**
     * Archives and deletes the requested project if it exists.
     *
     * @param string $project The project slug or identifier.
     * @return array The normalized response payload describing the delete result.
     */
    public function execute(string $project): array
    {
        if (!$this->archiveHandler->exists($project)) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        $archived = $this->archiveHandler->archive($project);
        if (!$archived) {
            return [
                'status' => 500,
                'data' => null,
                'meta' => [
                    'message' => 'Project archive failed.',
                ],
            ];
        }

        $deleted = $this->archiveHandler->delete($project);
        if (!$deleted) {
            return [
                'status' => 500,
                'data' => null,
                'meta' => [
                    'message' => 'Project delete failed after archive.',
                ],
            ];
        }

        return [
            'status' => 200,
            'data' => [
                'project' => $project,
                'deleted' => true,
                'archived' => true,
            ],
            'meta' => [
                'message' => 'Project deleted successfully.',
            ],
        ];
    }
}
