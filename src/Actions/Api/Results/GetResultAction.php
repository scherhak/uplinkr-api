<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use JsonException;
use UplinkrApi\Support\Api\ResultReader;

/**
 * Class GetResultAction
 * @package UplinkrApi\Actions\Api\Results
 *
 * Resolves a single stored probe result by its API identifier.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class GetResultAction
{
    /**
     * Initializes the action with the result reader dependency.
     *
     * @param ResultReader $resultReader The reader used to locate stored probe results.
     * @return void
     */
    public function __construct(
        private readonly ResultReader $resultReader,
    )
    {
    }

    /**
     * Returns the stored probe result matching the given identifier.
     *
     * @param string $resultId The API-specific result identifier.
     * @return array The normalized response payload containing the result or an error.
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
