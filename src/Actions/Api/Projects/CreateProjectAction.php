<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Project\InitHandler;
use Uplinkr\Handler\Project\ListHandler;

/**
 * Class CreateProjectAction
 * @package UplinkrApi\Actions\Api\Projects
 *
 * Creates a new project through the base Uplinkr handlers and returns a
 * normalized REST response payload.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class CreateProjectAction
{
    /**
     * Initializes the action with the project creation and lookup handlers.
     *
     * @param InitHandler $initHandler The handler that creates or initializes a project.
     * @param ListHandler $listHandler The handler used to detect existing projects.
     * @return void
     */
    public function __construct(
        private readonly InitHandler $initHandler,
        private readonly ListHandler $listHandler,
    ) {
    }

    /**
     * Validates the request, prevents duplicates, and creates a project.
     *
     * @param Request $request The incoming HTTP request containing the project payload.
     * @return array The normalized response payload for the create operation.
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
        if ($this->hasProject($projectName)) {
            return [
                'status' => 409,
                'data' => null,
                'meta' => [
                    'message' => 'Project already exists.',
                ],
            ];
        }

        $this->initHandler->handle([
            'project' => $projectName,
            'label' => $data['label'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        return [
            'status' => 201,
            'data' => [
                'project' => $projectName,
            ],
        ];
    }

    /**
     * Determines whether a project with the given name already exists.
     *
     * @param string $project The project slug or identifier.
     * @return bool True when the project already exists, false otherwise.
     */
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
