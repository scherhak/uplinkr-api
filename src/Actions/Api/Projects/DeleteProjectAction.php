<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Uplinkr\Handler\Project\Archive\ArchiveHandler;

class DeleteProjectAction
{
    public function __construct(
        private readonly ArchiveHandler $archiveHandler,
    ) {
    }

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
            'status' => 204,
            'data' => null,
        ];
    }
}
