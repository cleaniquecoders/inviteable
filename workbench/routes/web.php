<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\User;

// Auto-login the seeded user for preview
Route::middleware('web')->group(function () {
    Route::get('/login-as-test', function () {
        $user = User::where('email', 'test@example.com')->first();
        auth()->login($user);

        return redirect('/invitations');
    })->name('login');
});
