<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Handle Redirect
    |--------------------------------------------------------------------------
    |
    | These value handle redirection on accepted invitation,
    | already accepted invitation and access denied route.
    | Change one of these value with your route name if needed.
    |
    */
    'redirect' => [
        'accepted_token'         => 'invitation.index',
        'already_accepted_token' => 'invitation.index',
        'middleware'             => 'invitation.access_denied',
    ],
];
