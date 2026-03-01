# CLAUDE.md — cleaniquecoders/inviteable

## Project Overview

`cleaniquecoders/inviteable` — A Laravel package providing a polymorphic invitation system. Any Eloquent model can have invitations (groups, classrooms, meetings, etc.) via the `HasInviteable` concern and `morphMany` relationships.

## Stack

- PHP: ^8.2
- Laravel: 11–12
- Testing: Pest 3 (BDD-style `it()` blocks)
- Code Style: Laravel Pint (laravel preset)
- Static Analysis: PHPStan/Larastan level 5
- CI: GitHub Actions (Pint → PHPStan → Pest matrix)
- Package Tools: Spatie Laravel Package Tools

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

- Framework: Pest 3 (BDD-style `it()` blocks, no `describe()` used)
- PHPStan Level: 5 (with Larastan + baseline)
- Pint Preset: laravel
- Arch Tests: strict types enforced, model extends verified, enums backed, traits verified
- Test DB: SQLite in-memory via Orchestra Testbench
- Test stubs: `tests/Stubs/User.php`
- Factory: `database/factories/InviteFactory.php` (namespace `CleaniqueCoders\Inviteable\Database\Factories`)

## Key Configuration

Published via `vendor:publish --tag=inviteable-config`. Config (`config/inviteable.php`) controls token length, expiry duration, and redirect routes for accepted/expired/revoked tokens and middleware access-denied.

## DO / DON'T

- ✅ DO use `declare(strict_types=1)` in all PHP files
- ✅ DO use Pest for tests (`it()` style)
- ✅ DO use Spatie Package Tools for service provider
- ✅ DO use enums for status fields
- ❌ DON'T use blueprint-macro — write standard Laravel migrations
- ❌ DON'T use PHPUnit syntax — use Pest
- ❌ DON'T use `$dates` property — use `casts()` method

## Preferences

- Traits go in `src/Concerns/`, not `src/Traits/`
- Exceptions use full suffix: `InvalidInvitationTokenException`, not `InvalidInvitationToken`
- Mailables use Laravel 11+ `Envelope`/`Content` API, not `build()` method
- Events use constructor promotion with `public readonly`
- Migration stubs use anonymous classes

## Gotchas

> **Gotcha:** The `InviteFactory` lives in `database/factories/` but needs the namespace
> `CleaniqueCoders\Inviteable\Database\Factories`. This requires an `autoload-dev` mapping
> in `composer.json` and a `newFactory()` override in the `Invite` model.

> **Gotcha:** PHPStan cannot resolve Eloquent magic properties (`$status`, `$token`,
> `$expired_at`) without `@property` docblocks on the model. Always add property annotations.
