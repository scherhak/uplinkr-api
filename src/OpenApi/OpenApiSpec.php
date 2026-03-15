<?php

declare(strict_types=1);

namespace UplinkrApi\OpenApi;

/**
 * @OA\Info(
 *     title="Uplinkr API",
 *     version="1.0.0",
 *     description="REST API for projects and probes in the Uplinkr ecosystem."
 * )
 * @OA\Server(
 *     url="/",
 *     description="Application base URL"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="uplinkrApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="X-Uplinkr-Api-Key",
 *     description="Send the configured Uplinkr API key as request header."
 * )
 * @OA\Tag(
 *     name="Projects",
 *     description="Project management endpoints"
 * )
 * @OA\Tag(
 *     name="Probes",
 *     description="Probe management endpoints"
 * )
 */
final class OpenApiSpec
{
}
