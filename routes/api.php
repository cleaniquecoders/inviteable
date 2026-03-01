<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Http\Controllers\Api\InvitationApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvitationApiController::class, 'index'])->name('inviteable.api.index');
Route::post('/', [InvitationApiController::class, 'store'])->name('inviteable.api.store');
Route::get('/{invite}', [InvitationApiController::class, 'show'])->name('inviteable.api.show');
Route::post('/{token}/accept', [InvitationApiController::class, 'accept'])->name('inviteable.api.accept');
Route::post('/{token}/decline', [InvitationApiController::class, 'decline'])->name('inviteable.api.decline');
Route::post('/{token}/revoke', [InvitationApiController::class, 'revoke'])->name('inviteable.api.revoke');
Route::delete('/{invite}', [InvitationApiController::class, 'destroy'])->name('inviteable.api.destroy');
