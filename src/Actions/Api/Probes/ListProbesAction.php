<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class ListProbesAction
 * @package UplinkrApi\Actions\Api\Probes
 *
 * Lists probes globally or scoped to a single project.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ListProbesAction
{
    /**
     * Initializes the action with the probe catalog dependency.
     *
     * @param ProjectProbeCatalog $catalog The catalog used to enumerate probes.
     * @return void
     */
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
    )
    {
    }

    /**
     * Builds a probe listing response from request filters and optional project scope.
     *
     * @param Request $request The incoming HTTP request containing optional query filters.
     * @param string|null $project An optional project name provided by a nested route.
     * @return array The normalized response payload with probe data and collection metadata.
     */
    public function execute(Request $request, ?string $project = null): array
    {
        $projectFilter = $project ?? trim((string)$request->query('project', ''));
        if ($projectFilter === '') {
            $projectFilter = null;
        }

        $probes = $this->catalog->listProbes($projectFilter);

        return [
            'data' => $probes,
            'meta' => [
                'count' => count($probes),
            ],
        ];
    }
}
