# uplinkr-api
REST API for Uplinkr monitoring data.

## API Endpoints

### Projects
- `GET /api/v1/projects`
- `POST /api/v1/projects`
- `GET /api/v1/projects/{project}`
- `PATCH /api/v1/projects/{project}`
- `DELETE /api/v1/projects/{project}`

### Probes
- `GET /api/v1/probes`
- `POST /api/v1/probes`
- `GET /api/v1/projects/{project}/probes`
- `POST /api/v1/projects/{project}/probes`
- `GET /api/v1/projects/{project}/probes/{probeId}`
- `PATCH /api/v1/projects/{project}/probes/{probeId}`
- `DELETE /api/v1/projects/{project}/probes/{probeId}`

## Swagger UI

- UI: `GET /api/docs`
- OpenAPI JSON: `GET /api/docs/openapi`

The package uses `darkaonline/l5-swagger` and scans the package `src/` directory for OpenAPI annotations.
An example annotation is available on [ProjectsController.php](/Users/sascha/develop/uplinkr-api/src/Http/Controllers/Api/ProjectsController.php#L15).

In a host Laravel app, generate the docs with:

```bash
php artisan l5-swagger:generate
```

## Best-Practice Notes

- API implementation uses Uplinkr handler classes directly (not artisan command execution).
- Reasons: lower overhead, no CLI output parsing, better testability and clearer error mapping.

## Security (Baseline)

- API key auth via `X-Uplinkr-Api-Key` (or optional Bearer token)
- Configurable key list via `UPLINKR_API_KEYS`
- Rate limiting via `throttle:uplinkr-api` (configurable)
- Swagger regeneration toggle via `UPLINKR_API_SWAGGER_GENERATE_ALWAYS`

### .env example

```env
UPLINKR_API_AUTH_ENABLED=true
UPLINKR_API_KEYS=replace-me-with-a-long-random-key
UPLINKR_API_RATE_LIMIT_ENABLED=true
UPLINKR_API_RATE_LIMIT_MAX_ATTEMPTS=60
UPLINKR_API_RATE_LIMIT_DECAY_MINUTES=1
UPLINKR_API_SWAGGER_GENERATE_ALWAYS=true
```
