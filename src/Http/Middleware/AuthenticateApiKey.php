<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateApiKey
{
    /**
     * @param Closure(Request): mixed $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!(bool) config('uplinkr-api.security.api_key.enabled', true)) {
            return $next($request);
        }

        $configuredKeys = config('uplinkr-api.security.api_key.keys', []);
        if (!is_array($configuredKeys) || $configuredKeys === []) {
            return $this->unauthorizedResponse();
        }

        $providedApiKey = $this->resolveProvidedKey($request);
        if ($providedApiKey === null) {
            return $this->unauthorizedResponse();
        }

        foreach ($configuredKeys as $configuredKey) {
            if (!is_string($configuredKey) || $configuredKey === '') {
                continue;
            }

            if (hash_equals($configuredKey, $providedApiKey)) {
                return $next($request);
            }
        }

        return $this->unauthorizedResponse();
    }

    private function resolveProvidedKey(Request $request): ?string
    {
        $header = (string) config('uplinkr-api.security.api_key.header', 'X-Uplinkr-Api-Key');
        $header = trim($header);

        if ($header !== '') {
            $value = $request->header($header);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        if ((bool) config('uplinkr-api.security.api_key.accept_bearer_token', true)) {
            $token = $request->bearerToken();
            if (is_string($token) && trim($token) !== '') {
                return trim($token);
            }
        }

        return null;
    }

    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
}
