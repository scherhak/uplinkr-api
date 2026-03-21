# Release v0.1.0

Initial public release of `scherhak/uplinkr-api`.

## Summary

`uplinkr-api` extends the CLI-first [`scherhak/uplinkr`](https://github.com/scherhak/uplinkr) package with a versioned REST API for managing monitoring projects, probes, and selected global settings in Laravel 12 applications.

This release establishes the first usable API baseline for the package.

## Included

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
- package targets PHP `^8.4` and Laravel `^12.0`

## Release Notes

- initial standalone package baseline created
- package metadata, provider wiring, and configuration cleaned up
- REST-first API structure introduced for projects and probes
- security baseline added
- OpenAPI documentation integrated
- GitHub Actions and PHPUnit setup aligned with the current package requirements

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
