<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Uplinkr\Handler\Project\ListHandler;

class GetProjectAction
{
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

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
