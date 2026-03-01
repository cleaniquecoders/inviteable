<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Http\Controllers\Api\InvitationApiController;
use CleaniqueCoders\Inviteable\Models\Invite;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Event::fake();
    Mail::fake();

    // Register API routes manually since config is set after service provider boots
    Route::prefix('api/invitations')
        ->middleware(['api'])
        ->group(function () {
            Route::get('/', [InvitationApiController::class, 'index'])->name('inviteable.api.index');
            Route::post('/', [InvitationApiController::class, 'store'])->name('inviteable.api.store');
            Route::get('/{invite}', [InvitationApiController::class, 'show'])->name('inviteable.api.show');
            Route::post('/{token}/accept', [InvitationApiController::class, 'accept'])->name('inviteable.api.accept');
            Route::post('/{token}/decline', [InvitationApiController::class, 'decline'])->name('inviteable.api.decline');
            Route::post('/{token}/revoke', [InvitationApiController::class, 'revoke'])->name('inviteable.api.revoke');
            Route::delete('/{invite}', [InvitationApiController::class, 'destroy'])->name('inviteable.api.destroy');
        });

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

it('lists invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Test 1',
        'token' => hash('sha256', 'api-list-1'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $this->user->invitations()->create([
        'name' => 'Test 2',
        'token' => hash('sha256', 'api-list-2'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    $response = $this->getJson('api/invitations');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('filters invitations by status', function () {
    $this->user->invitations()->create([
        'name' => 'Pending',
        'token' => hash('sha256', 'api-filter-pending'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => hash('sha256', 'api-filter-accepted'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    $response = $this->getJson('api/invitations?status=pending');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.status', 'pending');
});

it('shows a single invitation', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Show Test',
        'token' => hash('sha256', 'api-show'),
        'status' => InvitationStatus::Pending,
    ]);

    $response = $this->getJson("api/invitations/{$invite->id}");

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Show Test');
});

it('does not expose token hash in response', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Token Test',
        'token' => hash('sha256', 'api-no-token'),
        'status' => InvitationStatus::Pending,
    ]);

    $response = $this->getJson("api/invitations/{$invite->id}");

    $response->assertOk();
    $response->assertJsonMissingPath('data.token');
});

it('deletes an invitation', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Delete Test',
        'token' => hash('sha256', 'api-delete'),
        'status' => InvitationStatus::Pending,
    ]);

    $response = $this->deleteJson("api/invitations/{$invite->id}");

    $response->assertOk();
    expect(Invite::find($invite->id))->toBeNull();
});
