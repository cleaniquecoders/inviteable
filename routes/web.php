<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('invitation/{token}', InvitationController::class)
    ->where('token', '[a-zA-Z0-9]+')
    ->name('invitation');

Route::view('invitation/access-denied', 'inviteable::errors.access-denied')
    ->name('invitation.access_denied');

Route::view('invitation', 'inviteable::invitations.index')
    ->name('invitation.index');
