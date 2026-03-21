<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Probe\UrlHandler;
use UplinkrApi\Support\Api\ProjectProbeCatalog;

/**
 * Class CreateResultAction
 * @package UplinkrApi\Actions\Api\Results
 *
 * Executes or queues a probe result run and returns a normalized API response.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class CreateResultAction
{
    /**
     * Initializes the action with the probe execution handler and probe catalog.
     *
     * @param UrlHandler $urlHandler The handler that executes or queues probe runs.
     * @param ProjectProbeCatalog $catalog The catalog used to validate project scope.
     * @return void
     */
    public function __construct(
        private readonly UrlHandler $urlHandler,
        private readonly ProjectProbeCatalog $catalog,
    ) {
    }

    /**
     * Validates the request payload and triggers a probe execution for the given scope.
     *
     * @param Request $request The incoming HTTP request with probe execution data.
     * @param string|null $project An optional project name from a nested route parameter.
     * @return array The normalized response payload containing the execution result.
     * @throws JsonException
     */
    public function execute(Request $request, ?string $project = null): array
    {
        $data = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'project' => ['nullable', 'string'],
            'method' => ['nullable', 'string', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'headers' => ['nullable', 'array'],
            'body' => ['nullable'],
            'latency' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $projectName = $project ?? (isset($data['project']) ? (string) $data['project'] : null);
        if (is_string($projectName) && $projectName !== '' && !$this->catalog->hasProject($projectName)) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Project not found.',
                ],
            ];
        }

        $result = $this->urlHandler
            ->with([
                'url' => (string) $data['url'],
                'project' => $projectName,
                'method' => (string) ($data['method'] ?? 'GET'),
                'headers' => $this->normalizeHeaders($data['headers'] ?? []),
                'body' => $data['body'] ?? null,
                'latency' => $data['latency'] ?? null,
            ])
            ->handle();

        if ($result === null) {
            return [
                'status' => 202,
                'data' => null,
                'meta' => [
                    'queued' => true,
                ],
            ];
        }

        return [
            'status' => 201,
            'data' => $result,
            'meta' => [
                'queued' => false,
            ],
        ];
    }

    /**
     * Normalizes request headers to the line-based format expected by the base handler.
     *
     * @param array $headers The raw headers payload from the request body.
     * @return array The normalized header lines.
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $normalized[] = $value;
                continue;
            }

            if (is_string($key) && is_scalar($value)) {
                $normalized[] = sprintf('%s: %s', $key, (string) $value);
            }
        }

        return $normalized;
    }
}
