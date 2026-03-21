<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Project\UpdateHandler;

/**
 * Class UpdateProjectAction
 * @package UplinkrApi\Actions\Api\Projects
 *
 * Applies partial updates to a stored project through the base package handler.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class UpdateProjectAction
{
    /**
     * Initializes the action with the project update handler dependency.
     *
     * @param UpdateHandler $updateHandler The handler that persists project changes.
     * @return void
     */
    public function __construct(
        private readonly UpdateHandler $updateHandler,
    )
    {
    }

    /**
     * Validates the request payload and applies the requested project updates.
     *
     * @param string $project The project slug or identifier.
     * @param Request $request The incoming HTTP request with partial update fields.
     * @return array The normalized response payload for the update operation.
     * @throws JsonException
     */
    public function execute(string $project, Request $request): array
    {
        $data = Validator::make($request->all(), [
            'label' => ['sometimes', 'nullable', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'string', 'in:enabled,disabled'],
        ])->validate();

        $options = ['project' => $project];

        if (array_key_exists('label', $data)) {
            $options['label'] = $data['label'];
        }

        if (array_key_exists('description', $data)) {
            $options['description'] = $data['description'];
        }

        if (array_key_exists('status', $data)) {
            $options['status'] = $data['status'];
        }

        $updated = $this->updateHandler->handle($options);
        if (!$updated) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        return [
            'status' => 200,
            'data' => [
                'project' => $project,
            ],
        ];
    }
}
