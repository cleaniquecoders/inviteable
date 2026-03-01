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
        'middleware' => 'invitation.access_denied',
    ],
];
