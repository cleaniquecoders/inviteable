# CLAUDE.md — cleaniquecoders/inviteable

## Project Overview

`cleaniquecoders/inviteable` — A Laravel package providing a polymorphic invitation system. Any Eloquent model can have invitations (groups, classrooms, meetings, etc.) via the `HasInviteable` concern and `morphMany` relationships.

## Stack

- PHP: ^8.2
- Laravel: 11–12
- Livewire: ^4.0
- FluxUI: ^2.0
- Testing: Pest 3 (BDD-style `it()` blocks)
- Code Style: Laravel Pint (laravel preset)
- Static Analysis: PHPStan/Larastan level 5
- CI: GitHub Actions (Pint -> PHPStan -> Pest matrix)
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

# Expire past-due invitations
php artisan inviteable:expire
```

## Architecture

- **Polymorphic design**: The `Invite` model uses `morphTo()` so any model using the `HasInviteable` concern gains an `invitations()` relationship.
- **Token hashing**: Tokens are stored as SHA-256 hashes. The `plainToken` transient property holds the unhashed token for email URLs.
- **Two-step acceptance**: GET shows confirmation page, POST accept/decline with CSRF.
- **Enum-based status**: `InvitationStatus` enum (`Pending`, `Accepted`, `Expired`, `Revoked`, `Declined`, `Cancelled`) with `label()` and `color()` helpers.
- **Event-driven flow**: `InvitationCreated` fires on model creation (via Eloquent `dispatchesEvents`), which triggers `SendInvitationEmail` listener (queued by default). `InvitationAccepted`, `InvitationAlreadyAccepted`, `InvitationDeclined`, `InvitationRevoked`, `InvitationCancelled` are dispatched by controller/manager.
- **Spatie Package Tools**: Service provider extends `PackageServiceProvider` for config, views, routes, and migrations.
- **Facade + Manager**: `Inviteable` facade resolves to `InviteableManager` providing `create()`, `createBatch()`, `accept()`, `decline()`, `revoke()`, `cancel()`, `findByToken()`, `resend()`.
- **Controller**: `InvitationController` with `show()`, `accept()`, `decline()` methods for token-based invitation handling.
- **API Controller**: `InvitationApiController` with full CRUD + accept/decline/revoke. Enabled via config.
- **Middleware**: `ValidateInvitationToken` validates active invitation tokens (hashes incoming token before lookup).
- **Rate Limiting**: Named rate limiter `inviteable` registered in service provider.
- **Livewire UI**: 5 components (Dashboard, Create, Detail, AcceptDecline, Settings) with FluxUI. Enabled via config.
- **Auto-expiry**: `inviteable:expire` artisan command updates past-due pending invitations to Expired.

## Testing

- Framework: Pest 3 (BDD-style `it()` blocks, no `describe()` used)
- PHPStan Level: 5 (with Larastan + baseline)
- Pint Preset: laravel
- Arch Tests: strict types enforced, model extends verified, enums backed, traits verified, Livewire extends Component, events use Dispatchable
- Test DB: SQLite in-memory via Orchestra Testbench
- Test stubs: `tests/Stubs/User.php`
- Factory: `database/factories/InviteFactory.php` (namespace `CleaniqueCoders\Inviteable\Database\Factories`)
- Livewire tests: require manually registering livewire routes in `beforeEach()` using `realpath(__DIR__.'/../../../routes/livewire.php')`

## Key Configuration

Published via `vendor:publish --tag=inviteable-config`. Config (`config/inviteable.php`) controls:
- Token length, expiry duration
- Rate limiting (max_attempts, decay_minutes)
- Auth requirement for acceptance (required, guard, redirect)
- Mail queue settings (queue, queue_connection, queue_name)
- Redirect routes for all statuses
- API routes (enabled, prefix, middleware)
- UI settings (enabled, prefix, middleware, layout)
- Inviteable types registry

## DO / DON'T

- ✅ DO use `declare(strict_types=1)` in all PHP files
- ✅ DO use Pest for tests (`it()` style)
- ✅ DO use Spatie Package Tools for service provider
- ✅ DO use enums for status fields
- ✅ DO hash tokens with `hash('sha256', $token)` before storing/querying
- ❌ DON'T use blueprint-macro — write standard Laravel migrations
- ❌ DON'T use PHPUnit syntax — use Pest
- ❌ DON'T use `$dates` property — use `casts()` method
- ❌ DON'T use `$guarded = []` — use explicit `$fillable`
- ❌ DON'T expose token hashes in API responses

## Preferences

- Traits go in `src/Concerns/`, not `src/Traits/`
- Exceptions use full suffix: `InvalidInvitationTokenException`, not `InvalidInvitationToken`
- Mailables use Laravel 11+ `Envelope`/`Content` API, not `build()` method
- Events use constructor promotion with `public readonly`
- Migration stubs use anonymous classes
- Livewire components go in `src/Http/Livewire/`

## Gotchas

> **Gotcha:** The `InviteFactory` lives in `database/factories/` but needs the namespace
> `CleaniqueCoders\Inviteable\Database\Factories`. This requires an `autoload-dev` mapping
> in `composer.json` and a `newFactory()` override in the `Invite` model.

> **Gotcha:** PHPStan cannot resolve Eloquent magic properties (`$status`, `$token`,
> `$expired_at`) without `@property` docblocks on the model. Always add property annotations.

> **Gotcha:** The `plainToken` property on `Invite` is transient (not persisted, not in fillable).
> It is only set by `InviteableManager::create()` and `resend()`. After a fresh query,
> `$invite->plainToken` will be null.

> **Gotcha:** Livewire routes are only registered when `config('inviteable.ui.enabled')` is true.
> In tests, register routes manually since config is set after the service provider boots.

> **Gotcha:** API routes are only registered when `config('inviteable.routes.api')` is true.
> Same config timing issue applies in tests.
