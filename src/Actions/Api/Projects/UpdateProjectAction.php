<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Project\UpdateHandler;

class UpdateProjectAction
{
    public function __construct(
        private readonly UpdateHandler $updateHandler,
    ) {
    }

    /**
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
