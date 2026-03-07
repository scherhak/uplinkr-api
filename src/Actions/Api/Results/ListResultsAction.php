<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Results;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Uplinkr\Objects\Config\UplinkrConfig;

class ListResultsAction
{
    public function __construct(
        private readonly UplinkrConfig $config,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(Request $request): array
    {
        $projectFilter = trim((string) $request->query('project', ''));
        $urlFilter = trim((string) $request->query('url', ''));
        $limit = max(1, min(500, (int) $request->query('limit', 100)));

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

                if ($projectFilter !== '' && $project !== $projectFilter) {
                    continue;
                }

                if ($urlFilter !== '' && $url !== $urlFilter) {
                    continue;
                }

                $results[] = $entry;

                if (count($results) >= $limit) {
                    break 2;
                }
            }
        }

        return [
            'data' => $results,
            'meta' => [
                'count' => count($results),
                'limit' => $limit,
            ],
        ];
    }
}
