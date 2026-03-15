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

The package uses `dedoc/scramble` and documents only the package API routes under `/api/v1`.
The example documentation metadata is implemented with Scramble PHP attributes in [ProjectsController.php](/Users/sascha/develop/uplinkr-api/src/Http/Controllers/Api/ProjectsController.php#L1).

Scramble renders docs dynamically. No generate step is required.

## Best-Practice Notes

- API implementation uses Uplinkr handler classes directly (not artisan command execution).
- Reasons: lower overhead, no CLI output parsing, better testability and clearer error mapping.

## Security (Baseline)

- API key auth via `X-Uplinkr-Api-Key` (or optional Bearer token)
- Configurable key list via `UPLINKR_API_KEYS`
- Rate limiting via `throttle:uplinkr-api` (configurable)
- Docs route protection can be customized via the host app's Scramble configuration or `viewApiDocs` gate.

### .env example

```env
UPLINKR_API_AUTH_ENABLED=true
UPLINKR_API_KEYS=replace-me-with-a-long-random-key
UPLINKR_API_RATE_LIMIT_ENABLED=true
UPLINKR_API_RATE_LIMIT_MAX_ATTEMPTS=60
UPLINKR_API_RATE_LIMIT_DECAY_MINUTES=1
```
