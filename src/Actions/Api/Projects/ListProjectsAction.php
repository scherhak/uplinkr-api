<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Uplinkr\Handler\Project\ListHandler;

/**
 * Class ListProjectsAction
 * @package UplinkrApi\Actions\Api\Projects
 *
 * Lists projects from the base Uplinkr storage and optionally filters by project name.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ListProjectsAction
{
    /**
     * Initializes the action with the project list handler dependency.
     *
     * @param ListHandler $listHandler The handler used to enumerate projects with details.
     * @return void
     */
    public function __construct(
        private readonly ListHandler $listHandler,
    )
    {
    }

    /**
     * Builds the projects collection response from the current request filter state.
     *
     * @param Request $request The incoming HTTP request containing optional query filters.
     * @return array The normalized response payload with project data and metadata.
     */
    public function execute(Request $request): array
    {
        $projectFilter = trim((string)$request->query('project', ''));
        $projects = $this->listHandler->allWithDetails();

        if ($projectFilter !== '') {
            $projects = array_values(array_filter($projects, static fn(array $project): bool => (string)($project['project'] ?? '') === $projectFilter
            ));
        }

        return [
            'data' => $projects,
            'meta' => [
                'count' => count($projects),
            ],
        ];
    }
}
