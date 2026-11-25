# Release v0.2.0

Project alert settings API and Laravel 13 compatibility.

## Summary

`uplinkr-api` v0.2.0 adds project-scoped alert settings endpoints backed by
Uplinkr v0.4.0 and expands support to Laravel 13.

The release is backward compatible with the existing v0.1.0 endpoints.

## Included

- all endpoints from v0.1.0
- project alert settings endpoints
  - `GET /projects/{project}/alerts`
  - `PATCH /projects/{project}/alerts`
- partial alert updates with validation and normalized responses
- TLS certificate alert activation and warning thresholds
- Uplinkr `^0.4.0`
- Scramble `^0.13`
- Laravel 12 and 13 compatibility
- versioned API routes under `/api/v1`
- project endpoints
  - `GET /projects`
  - `POST /projects`
  - `GET /projects/{project}`
  - `PATCH /projects/{project}`
  - `DELETE /projects/{project}`
- probe endpoints
  - `GET /probes`
  - `POST /probes`
  - `GET /projects/{project}/probes`
  - `POST /projects/{project}/probes`
  - `GET /projects/{project}/probes/{probeId}`
  - `PATCH /projects/{project}/probes/{probeId}`
  - `DELETE /projects/{project}/probes/{probeId}`
- global settings endpoints for I-am-alive configuration
  - `GET /settings/iam-alive`
  - `PATCH /settings/iam-alive`
- API key authentication
- rate limiting
- interactive API documentation via Scramble under `/api/docs`
- Postman collection for the current API surface
- PHPUnit/Testbench test setup with endpoint coverage

## Technical Notes

- namespace separated from the base package via `UplinkrApi\\...`
- API implementation uses Uplinkr handlers and storage classes directly
- result endpoints are intentionally not part of the current public API surface
- package targets PHP `^8.4` and Laravel `^12.0|^13.0`
- no breaking route or storage changes
- no migration is required

## Release Notes

- project alert resources added to the REST and OpenAPI surfaces
- Postman collection expanded with project alert requests
- validation rejects empty payloads, duplicate thresholds, unsupported channels,
  and non-positive values
- legacy projects receive stable certificate alert defaults in API responses

## Installation

```bash
composer require scherhak/uplinkr-api
```

## Documentation

- README: [README.md](README.md)
- Postman collection: [postman_collection.json](postman_collection.json)

## Repository

- Source: https://github.com/scherhak/uplinkr-api
- Issues: https://github.com/scherhak/uplinkr-api/issues
