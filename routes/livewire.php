<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inviteable::livewire.pages.dashboard');
})->name('inviteable.dashboard');

Route::get('/create', function () {
    return view('inviteable::livewire.pages.create');
})->name('inviteable.create');

Route::get('/{invite}', function (Invite $invite) {
    return view('inviteable::livewire.pages.detail', ['invite' => $invite]);
})->name('inviteable.detail');

Route::get('/settings/view', function () {
    return view('inviteable::livewire.pages.settings');
})->name('inviteable.settings');
