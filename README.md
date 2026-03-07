# uplinkr-api
REST API for Uplinkr monitoring data - access probes, results, alerts and system status programmatically.

## API Endpoints (MVP)

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

### Results
- `GET /api/v1/results`
- `POST /api/v1/results`
- `GET /api/v1/results/{resultId}`
- `GET /api/v1/projects/{project}/results`
- `POST /api/v1/projects/{project}/results`

## Best-Practice Notes

- API implementation uses Uplinkr handler classes directly (not artisan command execution).
- Reasons: lower overhead, no CLI output parsing, better testability and clearer error mapping.

## Security (Baseline)

- API key auth via `X-Uplinkr-Api-Key` (or optional Bearer token)
- Configurable key list via `UPLINKR_API_KEYS`
- Rate limiting via `throttle:uplinkr-api` (configurable)

### .env example

```env
UPLINKR_API_AUTH_ENABLED=true
UPLINKR_API_KEYS=replace-me-with-a-long-random-key
UPLINKR_API_RATE_LIMIT_ENABLED=true
UPLINKR_API_RATE_LIMIT_MAX_ATTEMPTS=60
UPLINKR_API_RATE_LIMIT_DECAY_MINUTES=1
```
