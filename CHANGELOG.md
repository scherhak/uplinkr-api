# Changelog

All notable changes to this project will be documented in this file.

## [0.2.0] - 2026-11-25

### Added

- Added `GET /api/v1/projects/{project}/alerts`
- Added `PATCH /api/v1/projects/{project}/alerts`
- Added normalized project alert resources and partial update validation
- Added certificate expiration alert settings from Uplinkr v0.4.0
- Added OpenAPI operation metadata and Postman requests for both endpoints

### Changed

- Updated `scherhak/uplinkr` requirement to `^0.4.0`
- Updated `dedoc/scramble` to `^0.13` for Laravel 13 support
- Added Laravel 13, Testbench 11, and PHPUnit 12 compatibility
- Retained Laravel 12 support

### Notes

- No breaking changes
- No storage migration is required

## [0.1.0] - 2026-03-22

- Initial public REST API release for projects, probes, and I-am-alive settings
