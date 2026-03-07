<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Uplinkr\Handler\Project\ListHandler;

class ListProjectsAction
{
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

    public function execute(Request $request): array
    {
        $projectFilter = trim((string) $request->query('project', ''));
        $projects = $this->listHandler->allWithDetails();

        if ($projectFilter !== '') {
            $projects = array_values(array_filter($projects, static fn (array $project): bool =>
                (string) ($project['project'] ?? '') === $projectFilter
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
