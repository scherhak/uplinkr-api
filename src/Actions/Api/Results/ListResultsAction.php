<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use Illuminate\Http\Request;
use JsonException;
use UplinkrApi\Support\Api\ResultReader;

/**
 * Class ListResultsAction
 * @package UplinkrApi\Actions\Api\Results
 *
 * Lists stored probe results with optional project and URL filtering.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ListResultsAction
{
    /**
     * Initializes the action with the result reader dependency.
     *
     * @param ResultReader $resultReader The reader used to enumerate stored probe results.
     * @return void
     */
    public function __construct(
        private readonly ResultReader $resultReader,
    )
    {
    }

    /**
     * Builds a result collection response using request filters and optional project scope.
     *
     * @param Request $request The incoming HTTP request containing optional query filters.
     * @param string|null $project An optional project name from a nested route parameter.
     * @return array The normalized response payload with result data and metadata.
     * @throws JsonException
     */
    public function execute(Request $request, ?string $project = null): array
    {
        $projectFilter = $project ?? trim((string)$request->query('project', ''));
        $urlFilter = trim((string)$request->query('url', ''));
        $limit = max(1, min(500, (int)$request->query('limit', 100)));

        $results = $this->resultReader->list(
            projectFilter: $projectFilter !== '' ? $projectFilter : null,
            urlFilter: $urlFilter !== '' ? $urlFilter : null,
            limit: $limit,
        );

        return [
            'data' => $results,
            'meta' => [
                'count' => count($results),
                'limit' => $limit,
            ],
        ];
    }
}
