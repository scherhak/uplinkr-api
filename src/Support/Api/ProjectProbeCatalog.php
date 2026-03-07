<?php

declare(strict_types=1);

namespace UplinkrApi\Support\Api;

use Uplinkr\Handler\Project\ListHandler;

class ProjectProbeCatalog
{
    public function __construct(
        private readonly ListHandler $listHandler,
    ) {
    }

    public function hasProject(string $projectName): bool
    {
        foreach ($this->listHandler->allWithDetails() as $project) {
            if ((string) ($project['project'] ?? '') === $projectName) {
                return true;
            }
        }

        return false;
    }

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

    public function findProbe(string $projectName, string $probeId): ?array
    {
        foreach ($this->listProbes($projectName) as $probe) {
            if ((string) ($probe['probe_id'] ?? '') === $probeId) {
                return $probe;
            }
        }

        return null;
    }

    public function buildProbeId(string $projectName, array $probe): string
    {
        $method = strtoupper((string) ($probe['method'] ?? 'GET'));
        $url = (string) ($probe['url'] ?? '');

        return sha1($projectName . '|' . $method . '|' . $url);
    }

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
