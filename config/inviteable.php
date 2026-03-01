<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Token Configuration
    |--------------------------------------------------------------------------
    */
    'token' => [
        'length' => 64,
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiry Configuration
    |--------------------------------------------------------------------------
    */
    'expiry' => [
        'duration' => 48, // hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'max_attempts' => 10,
        'decay_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | When 'required' is true, users must be authenticated to accept
    | or decline invitations. If not authenticated, they will be
    | redirected to the 'redirect' route.
    |
    */
    'auth' => [
        'required' => false,
        'guard' => null,
        'redirect' => 'login',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail Configuration
    |--------------------------------------------------------------------------
    */
    'mail' => [
        'queue' => true,
        'queue_connection' => null,
        'queue_name' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect Routes
    |--------------------------------------------------------------------------
    |
    | These values handle redirection on accepted invitation,
    | already accepted invitation, expired token, revoked token,
    | and access denied route. Change these with your route names.
    |
    */
    'redirect' => [
        'accepted_token' => 'invitation.index',
        'already_accepted_token' => 'invitation.index',
        'expired_token' => 'invitation.index',
        'revoked_token' => 'invitation.index',
        'declined_token' => 'invitation.index',
        'middleware' => 'invitation.access_denied',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'api' => false,
        'api_prefix' => 'api/invitations',
        'api_middleware' => ['api', 'auth:sanctum'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire UI
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'enabled' => false,
        'prefix' => 'invitations',
        'middleware' => ['web', 'auth'],
        'layout' => 'layouts.app',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inviteable Types
    |--------------------------------------------------------------------------
    |
    | Register the model types that can be invited. Used by the UI
    | to populate the inviteable type selector.
    |
    */
    'inviteable_types' => [
        // 'User' => \App\Models\User::class,
    ],
];
