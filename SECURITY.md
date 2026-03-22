# Security Policy

## Supported Versions

We currently provide security updates for the latest released minor version of Uplinkr API.

| Version | Supported |
| ------- | --------- |
| 0.1.x   | Yes       |

Security fixes are published to the default branch and released as patch versions when possible.

## Reporting a Vulnerability

Please report security issues privately by email:

- Email: sascha@uplinkr.dev

Do not open public GitHub issues for security vulnerabilities.

## What to Include

When reporting a vulnerability, please include:

- a short description of the issue
- steps to reproduce
- affected versions
- potential impact
- any suggested mitigation or fix, if available

## Response Process

- Initial response target: within 48 hours
- Status updates: provided during investigation when appropriate
- Public disclosure: after a fix is available or coordinated disclosure is agreed

## Scope Notes

For Uplinkr API, security-sensitive areas may include:

- API key authentication
- Bearer token support
- rate limiting behavior
- HTTP request validation and response handling
- interaction with file-based Uplinkr storage

If you are unsure whether an issue belongs to Uplinkr API, the base `scherhak/uplinkr` package, or project configuration, report it privately anyway.
