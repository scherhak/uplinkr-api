<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use JsonException;
use UplinkrApi\Support\Api\ResultReader;

class GetResultAction
{
    public function __construct(
        private readonly ResultReader $resultReader,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(string $resultId): array
    {
        $result = $this->resultReader->findByProbeResultId($resultId);
        if ($result === null) {
            return [
                'status' => 404,
                'data' => null,
                'meta' => [
                    'message' => 'Result not found.',
                ],
            ];
        }

        return [
            'data' => $result,
        ];
    }
}
