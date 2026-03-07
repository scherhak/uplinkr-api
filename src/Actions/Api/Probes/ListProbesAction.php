<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Probes;

use Illuminate\Http\Request;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

class ListProbesAction
{
    public function __construct(
        private readonly ProjectProbeCatalog $catalog,
    ) {
    }

    public function execute(Request $request, ?string $project = null): array
    {
        $projectFilter = $project ?? trim((string) $request->query('project', ''));
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
