<p align="center">
  <img src=".github/uplinkr-api-mascot-150.png" alt="Uplinkr Logo" width="270">
</p>

<h1 align="center" style="margin-top: -3rem;">Uplinkr API</h1>

<p align="center">
  REST API for Uplinkr monitoring projects, probes, and global settings.<br>
  Versioned Laravel endpoints for managing Uplinkr over HTTP.
</p>

<p align="center">
  <a href="https://github.com/scherhak/uplinkr-api/actions/workflows/tests.yml" style="text-decoration: none; border-bottom: none;">
    <img src="https://github.com/scherhak/uplinkr-api/actions/workflows/tests.yml/badge.svg" alt="tests">
  </a>
  <a href="https://packagist.org/packages/scherhak/uplinkr-api" style="text-decoration: none; border-bottom: none;">
    <img src="https://img.shields.io/packagist/v/scherhak/uplinkr-api" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/scherhak/uplinkr-api" style="text-decoration: none; border-bottom: none;">
    <img src="https://img.shields.io/packagist/dt/scherhak/uplinkr-api" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/scherhak/uplinkr-api" style="text-decoration: none; border-bottom: none;">
    <img src="https://img.shields.io/packagist/l/scherhak/uplinkr-api" alt="License">
  </a>
</p>

REST API for [Uplinkr](https://github.com/scherhak/uplinkr) monitoring projects, probes, and global settings.

Uplinkr API extends the CLI-first Uplinkr package with HTTP endpoints so monitoring data and configuration can be managed from external systems, internal tooling, or test clients such as Postman.

## What is Uplinkr API?

**Uplinkr API** is a lightweight Laravel package that exposes selected Uplinkr functionality through a versioned REST API.

It is designed for teams that want to:

- manage Uplinkr projects and probes over HTTP
- integrate monitoring setup into internal tools or deployment flows
- inspect and update global monitoring settings programmatically
- use OpenAPI-based API documentation during development

The package stays close to the architecture of the base package:

- file-based storage remains the source of truth
- API routes are explicit and versioned under `/api/v1`
- package logic prefers direct integration with handlers and storage classes
- no dashboard, no SPA, no additional persistence layer

## Key Features

- **REST API for Uplinkr** with versioned endpoints under `/api/v1`
- **Project management** for listing, creating, updating, and deleting projects
- **Probe management** with project-scoped probe resources
- **Global settings endpoint** for I-am-alive configuration
- **API key authentication** with optional Bearer token support
- **Rate limiting** for the API surface
- **Interactive API docs** via OpenAPI UI under `/api/docs`
- **Direct integration with Uplinkr internals** instead of shelling out to Artisan commands

## How It Works

Uplinkr API is installed into a Laravel application that already uses, or should use, Uplinkr.

The package registers:

1. versioned API routes
2. JSON controllers and actions
3. API-key based request protection
4. interactive OpenAPI documentation

The API delegates to the underlying Uplinkr package:

- project endpoints use Uplinkr project handlers
- probe endpoints use Uplinkr probe handlers
- settings endpoints use `Uplinkr\Storage\FileSettingsStorage`

This keeps the API predictable and avoids coupling HTTP behavior to CLI output.

## Quick Start

#### 1. Install the package

```bash
composer require scherhak/uplinkr-api
```

`scherhak/uplinkr` is installed as a dependency.

#### 2. Optionally publish the API config

```bash
php artisan vendor:publish --tag=uplinkr-api-config
```

Publishing is optional. The package works without publishing because config values are merged automatically.

#### 3. Configure API access

```env
UPLINKR_API_AUTH_ENABLED=true
UPLINKR_API_AUTH_HEADER=X-Uplinkr-Api-Key
UPLINKR_API_KEYS=replace-me-with-a-long-random-key
UPLINKR_API_ACCEPT_BEARER_TOKEN=false

UPLINKR_API_RATE_LIMIT_ENABLED=true
UPLINKR_API_RATE_LIMIT_MAX_ATTEMPTS=60
UPLINKR_API_RATE_LIMIT_DECAY_MINUTES=1
```

#### 4. Clear config cache

```bash
php artisan optimize:clear
```

#### 5. Open the API docs

- UI: `GET /api/docs`
- OpenAPI JSON: `GET /api/docs/openapi`

## Authentication

By default, the API expects an API key in a custom header:

```http
X-Uplinkr-Api-Key: replace-me-with-a-long-random-key
```

If Bearer support is enabled, the following is also accepted:

```http
Authorization: Bearer replace-me-with-a-long-random-key
```

If authentication is enabled and the key is missing or invalid, the API responds with `401 Unauthorized`.

## API Documentation

The package uses [`dedoc/scramble`](https://github.com/dedoc/scramble) to provide interactive API documentation.

- UI: `GET /api/docs`
- OpenAPI JSON: `GET /api/docs/openapi`

Documentation is rendered dynamically from the registered package routes. No generate step is required.

## Available Endpoints

### Projects

| Method | URI |
| --- | --- |
| `GET` | `/api/v1/projects` |
| `POST` | `/api/v1/projects` |
| `GET` | `/api/v1/projects/{project}` |
| `PATCH` | `/api/v1/projects/{project}` |
| `DELETE` | `/api/v1/projects/{project}` |

### Probes

| Method | URI |
| --- | --- |
| `GET` | `/api/v1/probes` |
| `POST` | `/api/v1/probes` |
| `GET` | `/api/v1/projects/{project}/probes` |
| `POST` | `/api/v1/projects/{project}/probes` |
| `GET` | `/api/v1/projects/{project}/probes/{probeId}` |
| `PATCH` | `/api/v1/projects/{project}/probes/{probeId}` |
| `DELETE` | `/api/v1/projects/{project}/probes/{probeId}` |

### Settings

| Method | URI |
| --- | --- |
| `GET` | `/api/v1/settings/iam-alive` |
| `PATCH` | `/api/v1/settings/iam-alive` |

Example request body for `PATCH /api/v1/settings/iam-alive`:

```json
{
  "enabled": true,
  "interval_hours": 6,
  "channels": ["mail", "webhook"]
}
```

## Example Requests

Create a project:

```bash
curl -X POST http://localhost/api/v1/projects \
  -H "X-Uplinkr-Api-Key: replace-me-with-a-long-random-key" \
  -H "Content-Type: application/json" \
  -d '{
    "project": "status-page",
    "label": "Status Page",
    "description": "Public status page monitoring"
  }'
```

Create a project probe:

```bash
curl -X POST http://localhost/api/v1/projects/status-page/probes \
  -H "X-Uplinkr-Api-Key: replace-me-with-a-long-random-key" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/health",
    "method": "GET",
    "latency": 1500
  }'
```

Read I-am-alive settings:

```bash
curl http://localhost/api/v1/settings/iam-alive \
  -H "X-Uplinkr-Api-Key: replace-me-with-a-long-random-key"
```

## Configuration

Default route prefix:

```env
UPLINKR_API_ROUTE_PREFIX=api/v1
```

Main configuration areas:

- route prefix and route middleware
- API key authentication header and accepted keys
- optional Bearer token support
- rate limiting behavior

If you need to customize package defaults locally, publish the config first:

```bash
php artisan vendor:publish --tag=uplinkr-api-config
```

## Design Decisions

- **Handlers over Commands**: The API uses Uplinkr handlers directly for projects and probes. This avoids shell execution, CLI output parsing, and unnecessary overhead.
- **Storage access for settings**: The settings API uses `Uplinkr\Storage\FileSettingsStorage` directly because the underlying resource is a single global settings document.
- **Versioned routing**: All package endpoints live under `/api/v1` to keep future evolution explicit.
- **Minimal scope**: The package intentionally exposes a focused subset of Uplinkr through HTTP instead of introducing a dashboard or separate application layer.

## Requirements

- **PHP:** 8.4 or higher
- **Laravel:** 12.x

## Development Notes

- Postman collection: [postman_collection.json](postman_collection.json)
- Package config: [config/uplinkr-api.php](config/uplinkr-api.php)
- Route definitions: [routes/api.php](routes/api.php)

## Contributing

Contributions are welcome. Keep changes explicit, small in scope, and aligned with the CLI-first Uplinkr architecture.

## Security

If you discover a security issue, please report it responsibly before opening a public issue.

## License

MIT License. See [LICENSE](LICENSE).
