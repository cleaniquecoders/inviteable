<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

beforeEach(function () {
    Event::fake();

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

it('has invites table', function () {
    expect(\Illuminate\Support\Facades\Schema::hasTable('invites'))->toBeTrue();
});

it('can create an invitation with pending status', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    expect($invite)
        ->toBeInstanceOf(Invite::class)
        ->isPending()->toBeTrue()
        ->isAccepted()->toBeFalse()
        ->isExpired()->toBeFalse()
        ->isRevoked()->toBeFalse();
});

it('casts status to enum', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect($invite->status)->toBe(InvitationStatus::Accepted);
});

it('detects expired invitation by timestamp', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Expired Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    expect($invite->isExpired())->toBeTrue();
});

it('detects expired invitation by status', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Expired Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Expired,
    ]);

    expect($invite->isExpired())->toBeTrue();
});

it('scopes pending invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Pending',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => Str::random(64),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect(Invite::pending()->count())->toBe(1);
    expect(Invite::accepted()->count())->toBe(1);
});

it('scopes active invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Active',
        'token' => 'active-token',
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $this->user->invitations()->create([
        'name' => 'Past Expiry',
        'token' => 'expired-token',
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    expect(Invite::active()->count())->toBe(1);
    expect(Invite::forToken('active-token')->active()->exists())->toBeTrue();
    expect(Invite::forToken('expired-token')->active()->exists())->toBeFalse();
});

it('scopes by token', function () {
    $token = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Find Me',
        'token' => $token,
        'status' => InvitationStatus::Pending,
    ]);

    expect(Invite::forToken($token)->first())->not->toBeNull();
    expect(Invite::forToken('nonexistent')->first())->toBeNull();
});
