<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

it('expires past-due pending invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Past Due',
        'token' => hash('sha256', 'past-due'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    $this->user->invitations()->create([
        'name' => 'Still Active',
        'token' => hash('sha256', 'still-active'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $this->artisan('inviteable:expire')
        ->expectsOutputToContain('Expired 1 invitation(s)')
        ->assertExitCode(0);

    expect(Invite::forToken(hash('sha256', 'past-due'))->first()->status)
        ->toBe(InvitationStatus::Expired);

    expect(Invite::forToken(hash('sha256', 'still-active'))->first()->status)
        ->toBe(InvitationStatus::Pending);
});

it('does not expire already accepted invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => hash('sha256', 'accepted-no-expire'),
        'status' => InvitationStatus::Accepted,
        'expired_at' => now()->subHour(),
        'accepted_at' => now()->subHours(2),
    ]);

    $this->artisan('inviteable:expire')
        ->expectsOutputToContain('Expired 0 invitation(s)')
        ->assertExitCode(0);
});

it('does not expire invitations without expiry date', function () {
    $this->user->invitations()->create([
        'name' => 'No Expiry',
        'token' => hash('sha256', 'no-expiry'),
        'status' => InvitationStatus::Pending,
        'expired_at' => null,
    ]);

    $this->artisan('inviteable:expire')
        ->expectsOutputToContain('Expired 0 invitation(s)')
        ->assertExitCode(0);
});
