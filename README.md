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
);
```

Or using the relationship directly:

```php
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use Illuminate\Support\Str;

$invite = $user->invitations()->create([
    'name' => 'Team Meeting',
    'token' => Str::random(64),
    'status' => InvitationStatus::Pending,
    'invited_by' => auth()->id(),
    'expired_at' => now()->addHours(48),
]);
```

### Accept & Revoke via Facade

```php
use CleaniqueCoders\Inviteable\Facades\Inviteable;

// Accept an invitation
$invite = Inviteable::accept($token);

// Revoke a pending invitation
$invite = Inviteable::revoke($token);

// Find by token
$invite = Inviteable::findByToken($token);
```

### Invitation Status

The `InvitationStatus` enum provides four states:

```php
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;

InvitationStatus::Pending;
InvitationStatus::Accepted;
InvitationStatus::Expired;
InvitationStatus::Revoked;
```

Model helpers:

```php
$invite->isPending();
$invite->isAccepted();
$invite->isExpired();
$invite->isRevoked();
```

### Query Scopes

```php
use CleaniqueCoders\Inviteable\Models\Invite;

Invite::pending()->get();
Invite::accepted()->get();
Invite::expired()->get();
Invite::revoked()->get();
Invite::active()->get();           // Pending + not past expiry
Invite::forToken($token)->first();
```

### Filtered Relationships

```php
$user->invitations;            // All invitations
$user->pendingInvitations;     // Only pending
$user->acceptedInvitations;    // Only accepted
```

### Events

| Event | Trigger |
|-------|---------|
| `InvitationCreated` | When an invitation is created |
| `InvitationAccepted` | When an invitation is accepted for the first time |
| `InvitationAlreadyAccepted` | When an already-accepted invitation is accessed |

A built-in listener (`SendInvitationEmail`) automatically sends an email when an invitation is created, if the inviteable model has an `email` attribute.

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

### Configuration

The config file (`config/inviteable.php`) controls:

```php
return [
    'token' => [
        'length' => 64,         // Token string length
    ],
    'expiry' => [
        'duration' => 48,       // Hours until expiry
    ],
    'redirect' => [
        'accepted_token' => 'invitation.index',
        'already_accepted_token' => 'invitation.index',
        'expired_token' => 'invitation.index',
        'revoked_token' => 'invitation.index',
        'middleware' => 'invitation.access_denied',
    ],
];
```

### Routes

The package registers these routes:

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/invitation/{token}` | `invitation` | Accept an invitation |
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

## Contributing

Everyone is welcome to contribute. Please provide:

1. The problem you solved
2. Tests
3. Documentation

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
