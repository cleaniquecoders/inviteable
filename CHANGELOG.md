# Changelog

All notable changes to `inviteable` will be documented in this file.

## v3.0.0 - 2026-03-01

### Breaking Changes

- **PHP 8.2+ required** (was PHP 7.3+)
- **Laravel 11/12 required** (was Laravel 5.5–8.0)
- **Removed** `cleaniquecoders/blueprint-macro` dependency
- **Replaced** boolean `is_accepted`/`is_expired` columns with `status` string column using `InvitationStatus` enum (`Pending`, `Accepted`, `Expired`, `Revoked`)
- **Renamed** trait namespace: `Traits\HasInviteable` → `Concerns\HasInviteable`
- **Renamed** middleware: `Http\Middleware\Inviteable` → `Http\Middleware\ValidateInvitationToken`
- **Renamed** mailable: `Mail\SendInvitationMail` → `Mail\InvitationMail`
- **Renamed** exception: `Exceptions\InvalidInvitationToken` → `Exceptions\InvalidInvitationTokenException`
- **Renamed** `LICENSE.txt` → `LICENSE.md`
- **Moved** listener: `Listeners\Invitations\SendInvitationEmail` → `Listeners\SendInvitationEmail`
- **Moved** resources from `src/` to package root (`config/`, `database/`, `resources/`, `routes/`)
- **Removed** old facade (`InviteableFacade`), replaced with `Facades\Inviteable`
- **Removed** `src/Http/Controllers/Controller.php` base controller
- **Removed** `src/Support/helpers.php`
- **Migration** is now a publishable stub (anonymous class)

### Added

- `InvitationStatus` enum with `Pending`, `Accepted`, `Expired`, `Revoked` states
- `InviteableManager` with `create()`, `accept()`, `revoke()`, `findByToken()` methods
- `Facades\Inviteable` facade
- Model scopes: `pending()`, `accepted()`, `expired()`, `revoked()`, `forToken()`, `active()`
- Boolean helpers: `isPending()`, `isAccepted()`, `isExpired()`, `isRevoked()`
- `pendingInvitations()` and `acceptedInvitations()` relationship helpers on trait
- Config options: `token.length`, `expiry.duration`, `redirect.expired_token`, `redirect.revoked_token`
- `InviteFactory` with states for accepted, expired, revoked
- Pest test suite with architecture tests
- GitHub Actions workflows (tests, Pint, PHPStan)
- Spatie Laravel Package Tools integration
- PHPStan / Larastan static analysis (level 5)
- Laravel Pint code style

### Fixed

- Middleware bug: old `Inviteable` middleware called non-existent `Invite::activeToken()` scope
- Controller now properly handles expired and revoked invitation states
- Listener uses `inviteable` relationship instead of raw model query

## v2.0.0 - 2020-09-22

- Added Laravel 8.0 Support

## v1.0.0

- Initial release
