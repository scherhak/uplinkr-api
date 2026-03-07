<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Project\InitHandler;
use Uplinkr\Handler\Project\ListHandler;

class CreateProjectAction
{
    public function __construct(
        private readonly InitHandler $initHandler,
        private readonly ListHandler $listHandler,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(Request $request): array
    {
        $data = Validator::make($request->all(), [
            'project' => ['required', 'string'],
            'label' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ])->validate();

        $projectName = (string) $data['project'];
        $alreadyExists = $this->hasProject($projectName);

        $this->initHandler->handle([
            'project' => $projectName,
            'label' => $data['label'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        return [
            'status' => $alreadyExists ? 200 : 201,
            'data' => [
                'project' => $projectName,
            ],
            'meta' => [
                'created' => !$alreadyExists,
            ],
        ];
    }

    private function hasProject(string $project): bool
    {
        foreach ($this->listHandler->allWithDetails() as $item) {
            if ((string) ($item['project'] ?? '') === $project) {
                return true;
            }
        }

        return false;
    }
}
