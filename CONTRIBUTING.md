# Contributing

Thanks for your interest in contributing to Uplinkr API.

## Core Principles

Before contributing, please align changes with the package direction:

- Keep the package lightweight and focused
- Prefer explicit behavior over implicit behavior
- Keep the API surface predictable and versioned
- Stay close to the architecture of the base `scherhak/uplinkr` package
- Avoid adding dashboards, extra persistence layers, or unnecessary abstractions

## Before You Start

- Check existing issues and discussions before starting work
- Open an issue first for bugs, proposals, or larger changes
- Discuss major changes before investing significant implementation effort
- Prefer small, focused pull requests over broad rewrites

## Development Setup

Requirements:

- PHP 8.4 or newer
- Composer
- Laravel 12.x

Installation:

```bash
composer install
```

Run tests:

```bash
./vendor/bin/phpunit
```

## Pull Request Guidelines

Required:

- Target the `main` branch
- Keep changes small in scope and easy to review
- Add or update tests for behavior changes
- Update README or related docs when user-facing behavior changes
- Preserve existing route structure and response semantics unless discussed first
- Avoid breaking changes for patch releases

Best practices:

- Keep commit messages clear and imperative
- Follow the existing coding style and package structure
- Prefer direct integration with Uplinkr handlers and storage over indirect wrappers
- Test both success and failure paths where applicable
- Be careful with authentication, rate limiting, and route versioning behavior

## Versioning Policy

Uplinkr API follows semantic versioning: `MAJOR.MINOR.PATCH`.

PATCH releases are for:

- bug fixes
- stability improvements
- tests
- documentation updates

MINOR releases are for:

- new API capabilities
- new endpoints
- backward-compatible configuration additions

MAJOR releases are for:

- breaking API changes
- route changes
- response format changes
- incompatible configuration changes

## Questions

If you have questions, you can:

- open a discussion on GitHub
- comment on a relevant issue
- reach out via email: sascha@uplinkr.dev
