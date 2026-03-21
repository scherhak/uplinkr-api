<?php

declare(strict_types=1);

namespace UplinkrApi\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthenticateApiKey
 * @package UplinkrApi\Http\Middleware
 *
 * Protects API routes with configured API keys and optional Bearer token support.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class AuthenticateApiKey
{
    /**
     * Validates the configured API authentication requirements for the current request.
     *
     * @param Request $request The incoming HTTP request to authorize.
     * @param Closure(Request): mixed $next
     * @return mixed The next middleware result or an unauthorized JSON response.
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

    /**
     * Resolves the API key provided either through the configured header or Bearer token.
     *
     * @param Request $request The incoming HTTP request containing authentication headers.
     * @return string|null The provided API key or null when no supported credential was supplied.
     */
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

    /**
     * Builds the standard unauthorized JSON response for failed authentication.
     *
     * @return JsonResponse The unauthorized response payload.
     */
    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
}
