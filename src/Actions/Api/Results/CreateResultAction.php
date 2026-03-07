<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Handler\Probe\UrlHandler;

class CreateResultAction
{
    public function __construct(
        private readonly UrlHandler $urlHandler,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(Request $request): array
    {
        $data = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'project' => ['nullable', 'string'],
            'method' => ['nullable', 'string', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'headers' => ['nullable', 'array'],
            'body' => ['nullable'],
            'latency' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $result = $this->urlHandler
            ->with([
                'url' => (string) $data['url'],
                'project' => isset($data['project']) ? (string) $data['project'] : null,
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
