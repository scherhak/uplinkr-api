<?php

declare(strict_types=1);

namespace UplinkrApi\Support\Api;

use Uplinkr\Handler\Project\ListHandler;

/**
 * Class ProjectProbeCatalog
 * @package UplinkrApi\Support\Api
 *
 * Provides normalized lookup and catalog operations for probes stored inside
 * Uplinkr projects.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class ProjectProbeCatalog
{
    /**
     * Initializes the catalog with the project list handler dependency.
     *
     * @param ListHandler $listHandler The handler used to enumerate projects with probe details.
     * @return void
     */
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

    /**
     * Determines whether a project exists in the underlying storage.
     *
     * @param string $projectName The project slug or identifier.
     * @return bool True when the project exists, false otherwise.
     */
    public function hasProject(string $projectName): bool
    {
        foreach ($this->listHandler->allWithDetails() as $project) {
            if ((string) ($project['project'] ?? '') === $projectName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all probes globally or scoped to the specified project.
     *
     * @param string|null $projectName An optional project name used to scope the result set.
     * @return array The normalized probe collection.
     */
    public function listProbes(?string $projectName = null): array
    {
        $probes = [];

        foreach ($this->listHandler->allWithDetails() as $project) {
            $currentProject = (string) ($project['project'] ?? '');
            if ($projectName !== null && $currentProject !== $projectName) {
                continue;
            }

            foreach (($project['probes'] ?? []) as $probe) {
                if (!is_array($probe)) {
                    continue;
                }

                $normalized = $this->normalizeProbe($probe, $currentProject);
                $normalized['probe_id'] = $this->buildProbeId($currentProject, $normalized);
                $probes[] = $normalized;
            }
        }

        return $probes;
    }

    /**
     * Returns a single probe for the given project and probe identifier.
     *
     * @param string $projectName The project slug or identifier.
     * @param string $probeId The API-specific probe identifier.
     * @return array|null The normalized probe payload or null when no match exists.
     */
    public function findProbe(string $projectName, string $probeId): ?array
    {
        foreach ($this->listProbes($projectName) as $probe) {
            if ((string) ($probe['probe_id'] ?? '') === $probeId) {
                return $probe;
            }
        }

        return null;
    }

    /**
     * Builds a stable probe identifier from the project name, method, and URL.
     *
     * @param string $projectName The project slug or identifier.
     * @param array $probe The probe payload used to derive the identifier.
     * @return string The generated probe identifier.
     */
    public function buildProbeId(string $projectName, array $probe): string
    {
        $method = strtoupper((string) ($probe['method'] ?? 'GET'));
        $url = (string) ($probe['url'] ?? '');

        return sha1($projectName . '|' . $method . '|' . $url);
    }

    /**
     * Normalizes a raw stored probe array for API output.
     *
     * @param array $probe The raw probe payload from storage.
     * @param string $projectName The owning project name used as fallback scope.
     * @return array The normalized probe payload.
     */
    private function normalizeProbe(array $probe, string $projectName): array
    {
        $probe['project'] = $probe['project'] ?? $projectName;
        $probe['method'] = strtoupper((string) ($probe['method'] ?? 'GET'));
        if (!array_key_exists('headers', $probe) && array_key_exists('header', $probe)) {
            $probe['headers'] = $probe['header'];
        }

        return $probe;
    }
}
