
[![Build Status](https://travis-ci.org/cleaniquecoders/inviteable.svg?branch=master)](https://travis-ci.org/cleaniquecoders/inviteable) [![Latest Stable Version](https://poser.pugx.org/cleaniquecoders/inviteable/v/stable)](https://packagist.org/packages/cleaniquecoders/inviteable) [![Total Downloads](https://poser.pugx.org/cleaniquecoders/inviteable/downloads)](https://packagist.org/packages/cleaniquecoders/inviteable) [![License](https://poser.pugx.org/cleaniquecoders/inviteable/license)](https://packagist.org/packages/cleaniquecoders/inviteable)

## About Your Package

Inviteable, inspired from [Laravel Auth Invitations](https://github.com/LaravelDaily/Laravel-Auth-Invitations), but in this package, not for Auth, but for anything. Yes, we mean anything! Invitation to group, to class room, to meeting. Can be anything!  

## Installation

1. In order to install `cleaniquecoders/inviteable` in your Laravel project, just run the *composer require* command from your terminal:

```
$ composer require cleaniquecoders/inviteable
```

2. Then in your `config/app.php` add the following to the providers array:

```php
CleaniqueCoders\Inviteable\InviteableServiceProvider::class,
```

3. Run the migration file:

```
$ php artisan migrate
```

## Usage

Inviteable provide a trait `\CleaniqueCoders\Inviteable\Traits\HasInviteable`. 

Following are the sample usage.

### Setup

```php
use CleaniqueCoders\Inviteable\Traits\HasInviteable;

class User extends Authenticatable 
{
	use HasInviteable;
}
```

### Creating Invitation

```
$invitation = User::create([
    'email'    => 'test@test.com',
    'name'     => 'Test Name',
    'password' => bcrypt('secret'),
])
    ->invitations()
    ->create([
        'name'       => 'Invitation',
        'token'      => str_random(64),
        'invited_by' => 1,
        'is_expired' => false,
        'expired_at' => \Carbon\Carbon::now()->addHours(24),
    ]);
```

Once you have create the invitation, you may use the invitation with events and notifications. 

Will add dispatching event on invitation created, so you can extend the use of the invitation to something else like notification.

More sample usage using `routes/console.php`:

```php
use App\User;

Artisan::command('invite', function() {
    // create a user that will invite other person
    $invitor = factory(User::class)->create();
    
    // to invite who
    $to_invite = factory(User::class)->create();
    
    // login using invitor
    auth()->loginUsingId($invitor->id);

    // invite user to a class
    $to_invite->invitations()->create([
        'name'       => 'Live Coding Class',
        'token'      => str_random(64),
        'invited_by' => auth()->user()->id,
        'is_expired' => false,
        'expired_at' => \Carbon\Carbon::now()->addHours(24),
    ]);
})->describe('Inivite the fastest way via cli.');
```

### Event and Listener

1. On Invitation Created - `\CleaniqueCoders\Inviteable\Events\InvitationAccepted`
2. On Invitation Accepted - `\CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted`
3. On Invitation Already Accepted - `\CleaniqueCoders\Inviteable\Events\InvitationCreated`

Added Listener to send out e-mail invitation:

1. `CleaniqueCoders\Inviteable\Listeners\Invitations` - You need to configure in your `app/Providers/EventServiceProvider` to have this in your app. 

```php
/**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        '\CleaniqueCoders\Inviteable\Events\InvitationCreated' => [
            '\CleaniqueCoders\Inviteable\Listeners\Invitations\SendInvitationEmail',
        ],
    ];
```

### Middleware

Added Middleware to be use to check only active invite able to get through

```php
'inviteable' => \CleaniqueCoders\Inviteable\Http\Middleware\Inviteable::class,
```

### Configuration

Added `config/inviteable.php` to handle redirection - using route name:

```php
<?php 

return [
    'redirect' => [
        'accepted_token' => 'invitation.index',
        'already_accepted_token' => 'invitation.index',
        'middleware' => 'invitation.access_denied'
    ],
];
```

### Route

Default route `php artisan route:list --name=invitation` consist of 

1. Activation invitation - on success, you will redirect to `inviteable.redirect.accepted_token` route. You may overwrite this. In this route also handle already accepted invitation. Do specify `inviteable.redirect.already_accepted_token` route name to redirect to other page.
2. Access denied route - You can change the redirect by specify route name in `config.redirect.middleware`

### Views

Run `php artisan vendor:publish --tag=inviteable` to publish configuration and views for Inviteable.

## Test

To run the test, type `vendor/bin/phpunit` in your terminal.

To have codes coverage, please ensure to install PHP XDebug then run the following command:

```
$ vendor/bin/phpunit -v --coverage-text --colors=never --stderr
```

## Contributions

Everyone are welcome to contribute to this package. However, it's a good practice to provide:

1. The problem you solved
2. Provide test
3. Documentation

Without these 3, you may add extra work for the maintainer.

## License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).