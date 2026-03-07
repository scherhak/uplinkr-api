<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use Illuminate\Http\Request;
use JsonException;
use UplinkrApi\Support\Api\ResultReader;

class ListResultsAction
{
    public function __construct(
        private readonly ResultReader $resultReader,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(Request $request, ?string $project = null): array
    {
        $projectFilter = $project ?? trim((string) $request->query('project', ''));
        $urlFilter = trim((string) $request->query('url', ''));
        $limit = max(1, min(500, (int) $request->query('limit', 100)));

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
