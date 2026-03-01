# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

`cleaniquecoders/inviteable` — A Laravel package providing a polymorphic invitation system. Any Eloquent model can have invitations (groups, classrooms, meetings, etc.) via the `HasInviteable` concern and `morphMany` relationships.

## Commands

```bash
# Run all tests
vendor/bin/pest

# Run a single test
vendor/bin/pest --filter="test name"

# Code style fixing
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse
```

## Architecture

- **Polymorphic design**: The `Invite` model uses `morphTo()` so any model using the `HasInviteable` concern gains an `invitations()` relationship.
- **Enum-based status**: `InvitationStatus` enum (`Pending`, `Accepted`, `Expired`, `Revoked`) replaces boolean columns.
- **Event-driven flow**: `InvitationCreated` fires on model creation (via Eloquent `dispatchesEvents`), which triggers `SendInvitationEmail` listener. `InvitationAccepted` and `InvitationAlreadyAccepted` are dispatched by `InvitationController`.
- **Spatie Package Tools**: Service provider extends `PackageServiceProvider` for config, views, routes, and migrations.
- **Facade + Manager**: `Inviteable` facade resolves to `InviteableManager` providing `create()`, `accept()`, `revoke()`, `findByToken()`.
- **Invokable controller**: `InvitationController` uses `__invoke()` to handle token-based invitation acceptance at `GET /invitation/{token}`.
- **Middleware**: `ValidateInvitationToken` middleware validates active invitation tokens before granting access.

## Testing

Tests use Pest with Orchestra Testbench and SQLite in-memory database. Test stubs are in `tests/Stubs/`. Feature tests cover invite model scopes, controller flows, trait relationships, and email dispatch. Arch tests verify strict types, model inheritance, and enum structure.

## Version Support

PHP ^8.2, Laravel 11–12, Orchestra Testbench 9–10, Pest 3.

## Key Configuration

Published via `vendor:publish --tag=inviteable-config`. Config (`config/inviteable.php`) controls token length, expiry duration, and redirect routes for accepted/expired/revoked tokens and middleware access-denied.
