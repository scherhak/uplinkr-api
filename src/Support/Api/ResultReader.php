<?php

declare(strict_types=1);

namespace UplinkrApi\Support\Api;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Uplinkr\Objects\Config\UplinkrConfig;

/**
 * Class ResultReader
 * @package UplinkrApi\Support\Api
 *
 * Reads probe result files from Uplinkr storage and exposes filtered lookup methods.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ResultReader
{
    /**
     * Initializes the result reader with the Uplinkr configuration dependency.
     *
     * @param UplinkrConfig $config The configuration used to resolve storage paths and file formats.
     * @return void
     */
    public function __construct(
        private readonly UplinkrConfig $config,
    ) {
    }

    /**
     * Lists stored probe results with optional project, URL, and limit filtering.
     *
     * @param string|null $projectFilter An optional project filter.
     * @param string|null $urlFilter An optional URL filter.
     * @param int $limit The maximum number of results to return, capped internally.
     * @return array The filtered result collection.
     * @throws JsonException
     */
    public function list(?string $projectFilter = null, ?string $urlFilter = null, int $limit = 100): array
    {
        $limit = $limit < 1 ? 0 : min(500, $limit);

        $disk = Storage::disk($this->config->getStorageDisc());
        $storagePath = $this->config->getStoragePath();
        $probeResultsPath = '/' . trim($this->config->getProbeResultsPath(), '/') . '/';
        $fileExtension = '.' . ltrim($this->config->getFileExtension(), '.');

        $files = array_values(array_filter(
            $disk->allFiles($storagePath),
            static fn (string $file): bool =>
                str_contains($file, $probeResultsPath)
                && str_ends_with($file, $fileExtension)
        ));

        rsort($files);

        $results = [];

        foreach ($files as $file) {
            $content = trim((string) $disk->get($file));
            if ($content === '') {
                continue;
            }

            try {
                $entries = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }

            if (!is_array($entries)) {
                continue;
            }

            foreach ($entries as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $project = (string) Arr::get($entry, 'settings.project', '');
                $url = (string) Arr::get($entry, 'settings.url', '');

                if ($projectFilter !== null && $projectFilter !== '' && $project !== $projectFilter) {
                    continue;
                }

                if ($urlFilter !== null && $urlFilter !== '' && $url !== $urlFilter) {
                    continue;
                }

                $results[] = $entry;

                if ($limit > 0 && count($results) >= $limit) {
                    break 2;
                }
            }
        }

        return $results;
    }

    /**
     * Returns a single stored result by its probe result identifier.
     *
     * @param string $probeResultId The API-specific result identifier.
     * @return array|null The matched result payload or null when no result is found.
     * @throws JsonException
     */
    public function findByProbeResultId(string $probeResultId): ?array
    {
        foreach ($this->list(limit: 0) as $entry) {
            if ((string) Arr::get($entry, 'probe_id', '') === $probeResultId) {
                return $entry;
            }
        }

        return null;
    }
}
