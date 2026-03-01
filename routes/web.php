<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('invitation/{token}', [InvitationController::class, 'show'])
    ->where('token', '[a-zA-Z0-9]+')
    ->middleware('throttle:inviteable')
    ->name('invitation.show');

Route::post('invitation/{token}/accept', [InvitationController::class, 'accept'])
    ->where('token', '[a-zA-Z0-9]+')
    ->middleware('throttle:inviteable')
    ->name('invitation.accept');

Route::post('invitation/{token}/decline', [InvitationController::class, 'decline'])
    ->where('token', '[a-zA-Z0-9]+')
    ->middleware('throttle:inviteable')
    ->name('invitation.decline');

Route::view('invitation/access-denied', 'inviteable::errors.access-denied')
    ->name('invitation.access_denied');

Route::view('invitation', 'inviteable::invitations.index')
    ->name('invitation.index');
