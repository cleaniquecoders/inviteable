# Inviteable

[![run-tests](https://github.com/cleaniquecoders/inviteable/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cleaniquecoders/inviteable/actions/workflows/run-tests.yml)
[![fix-php-code-style](https://github.com/cleaniquecoders/inviteable/actions/workflows/fix-php-code-style.yml/badge.svg)](https://github.com/cleaniquecoders/inviteable/actions/workflows/fix-php-code-style.yml)
[![phpstan](https://github.com/cleaniquecoders/inviteable/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cleaniquecoders/inviteable/actions/workflows/phpstan.yml)
[![Latest Stable Version](https://poser.pugx.org/cleaniquecoders/inviteable/v/stable)](https://packagist.org/packages/cleaniquecoders/inviteable)
[![Total Downloads](https://poser.pugx.org/cleaniquecoders/inviteable/downloads)](https://packagist.org/packages/cleaniquecoders/inviteable)
[![License](https://poser.pugx.org/cleaniquecoders/inviteable/license)](https://packagist.org/packages/cleaniquecoders/inviteable)

A polymorphic invitation system for Laravel. Any Eloquent model can have invitations — groups, classrooms, meetings, anything.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Livewire 4+ (included as dependency)
- FluxUI 2+ (included as dependency)

## Installation

```bash
composer require cleaniquecoders/inviteable
```

Publish and run the migration:

```bash
php artisan vendor:publish --tag=inviteable-migrations
php artisan migrate
```

Optionally publish the config:

```bash
php artisan vendor:publish --tag=inviteable-config
```

## Security

### Token Hashing

Invitation tokens are stored as SHA-256 hashes in the database. The plaintext token is only sent via email links and never persisted. This means a database breach does not expose valid invitation URLs.

### Two-Step Acceptance

Invitation acceptance uses a two-step flow:

1. `GET /invitation/{token}` shows a confirmation page with Accept/Decline buttons
2. `POST /invitation/{token}/accept` or `POST /invitation/{token}/decline` performs the action with CSRF protection

### Rate Limiting

All invitation routes are rate-limited (configurable via `rate_limit.max_attempts` and `rate_limit.decay_minutes`).

### Configurable Authentication

Optionally require authentication before accepting/declining invitations:

```php
// config/inviteable.php
'auth' => [
    'required' => true,
    'guard' => null,
    'redirect' => 'login',
],
```

## Usage

### Add the Trait

Add the `HasInviteable` concern to any model that should have invitations:

```php
use CleaniqueCoders\Inviteable\Concerns\HasInviteable;

class User extends Authenticatable
{
    use HasInviteable;
}
```

### Create Invitations

Using the facade:

```php
use CleaniqueCoders\Inviteable\Facades\Inviteable;

$invite = Inviteable::create(
    inviteable: $user,
    name: 'Team Meeting',
    invitedBy: auth()->id(),
    metadata: ['role' => 'member'],
);

// $invite->plainToken contains the unhashed token for email links
```

### Batch Invitations

Create multiple invitations in a single transaction:

```php
$invitations = Inviteable::createBatch($user, [
    ['name' => 'Alice'],
    ['name' => 'Bob', 'metadata' => ['role' => 'admin']],
    ['name' => 'Charlie', 'expiry_hours' => 72],
], invitedBy: auth()->id());
```

### Accept, Decline, Revoke & Cancel

```php
use CleaniqueCoders\Inviteable\Facades\Inviteable;

$invite = Inviteable::accept($plainToken);
$invite = Inviteable::decline($plainToken);
$invite = Inviteable::revoke($plainToken);
$invite = Inviteable::cancel($plainToken);
$invite = Inviteable::findByToken($plainToken);
```

### Resend Invitation

Regenerates the token, resets expiry, and triggers the email:

```php
$invite = Inviteable::resend($plainToken);
// or pass the model directly
$invite = Inviteable::resend($inviteModel);
```

### Invitation Status

The `InvitationStatus` enum provides six states:

```php
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;

InvitationStatus::Pending;
InvitationStatus::Accepted;
InvitationStatus::Expired;
InvitationStatus::Revoked;
InvitationStatus::Declined;
InvitationStatus::Cancelled;

// Presentation helpers
$status->label(); // "Pending"
$status->color(); // "yellow"
```

Model helpers:

```php
$invite->isPending();
$invite->isAccepted();
$invite->isExpired();
$invite->isRevoked();
$invite->isDeclined();
$invite->isCancelled();
```

### Query Scopes

```php
use CleaniqueCoders\Inviteable\Models\Invite;

Invite::pending()->get();
Invite::accepted()->get();
Invite::expired()->get();
Invite::revoked()->get();
Invite::declined()->get();
Invite::cancelled()->get();
Invite::active()->get();           // Pending + not past expiry
Invite::forToken($hashedToken)->first();
```

### Filtered Relationships

```php
$user->invitations;              // All invitations
$user->pendingInvitations;       // Only pending
$user->acceptedInvitations;      // Only accepted
$user->declinedInvitations;      // Only declined
$user->revokedInvitations;       // Only revoked
```

### Metadata

Store arbitrary data with invitations:

```php
$invite = Inviteable::create(
    inviteable: $user,
    name: 'Project Invite',
    metadata: ['role' => 'editor', 'department' => 'engineering'],
);

$invite->metadata; // ['role' => 'editor', 'department' => 'engineering']
```

### Auto-Expiry Command

Expire past-due pending invitations:

```bash
php artisan inviteable:expire
```

Schedule it in your application:

```php
Schedule::command('inviteable:expire')->hourly();
```

### Events

| Event | Trigger |
|-------|---------|
| `InvitationCreated` | When an invitation is created |
| `InvitationAccepted` | When an invitation is accepted |
| `InvitationAlreadyAccepted` | When an already-accepted invitation is accessed |
| `InvitationDeclined` | When an invitation is declined |
| `InvitationRevoked` | When an invitation is revoked |
| `InvitationCancelled` | When an invitation is cancelled |

A built-in listener (`SendInvitationEmail`) automatically queues an email when an invitation is created, if the inviteable model has an `email` attribute. Configure via:

```php
'mail' => [
    'queue' => true,          // Set false for synchronous sending
    'queue_connection' => null,
    'queue_name' => null,
],
```

### API Routes

Enable REST API endpoints:

```php
// config/inviteable.php
'routes' => [
    'api' => true,
    'api_prefix' => 'api/invitations',
    'api_middleware' => ['api', 'auth:sanctum'],
],
```

Available endpoints:

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/invitations` | List invitations (filterable by `status`, `inviteable_type`) |
| POST | `/api/invitations` | Create invitation |
| GET | `/api/invitations/{id}` | Show invitation |
| POST | `/api/invitations/{token}/accept` | Accept invitation |
| POST | `/api/invitations/{token}/decline` | Decline invitation |
| POST | `/api/invitations/{token}/revoke` | Revoke invitation |
| DELETE | `/api/invitations/{id}` | Delete invitation |

The API never exposes token hashes in responses.

### Livewire UI Dashboard

Enable the full Livewire + FluxUI management dashboard:

```php
// config/inviteable.php
'ui' => [
    'enabled' => true,
    'prefix' => 'invitations',
    'middleware' => ['web', 'auth'],
    'layout' => 'layouts.app',
],

'inviteable_types' => [
    'User' => \App\Models\User::class,
],
```

Dashboard routes:

| Route | Component | Description |
|-------|-----------|-------------|
| `/invitations` | InvitationDashboard | List, search, filter, sort, bulk actions |
| `/invitations/create` | CreateInvitation | Create single or batch invitations |
| `/invitations/{id}` | InvitationDetail | View details, audit info, metadata, actions |
| `/invitations/settings/view` | InvitationSettings | Display current configuration |

The confirmation page (`GET /invitation/{token}`) also uses a Livewire component when UI is enabled.

### Middleware

Register the middleware to protect routes requiring a valid invitation token:

```php
// bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'inviteable' => \CleaniqueCoders\Inviteable\Http\Middleware\ValidateInvitationToken::class,
    ]);
})
```

Then use it in routes:

```php
Route::get('event/{token}', EventController::class)->middleware('inviteable');
```

### Web Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/invitation/{token}` | `invitation.show` | Show confirmation page |
| POST | `/invitation/{token}/accept` | `invitation.accept` | Accept invitation |
| POST | `/invitation/{token}/decline` | `invitation.decline` | Decline invitation |
| GET | `/invitation/access-denied` | `invitation.access_denied` | Access denied page |
| GET | `/invitation` | `invitation.index` | Invitation landing page |

### Views

Publish views for customization:

```bash
php artisan vendor:publish --tag=inviteable-views
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADE](UPGRADE.md) for migration guide from previous versions.

## Contributing

Everyone is welcome to contribute. Please provide:

1. The problem you solved
2. Tests
3. Documentation

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
