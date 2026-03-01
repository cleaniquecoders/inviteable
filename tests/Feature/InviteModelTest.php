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

it('has invites table', function () {
    expect(\Illuminate\Support\Facades\Schema::hasTable('invites'))->toBeTrue();
});

it('can create an invitation with pending status', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => hash('sha256', 'test-token'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    expect($invite)
        ->toBeInstanceOf(Invite::class)
        ->isPending()->toBeTrue()
        ->isAccepted()->toBeFalse()
        ->isExpired()->toBeFalse()
        ->isRevoked()->toBeFalse()
        ->isDeclined()->toBeFalse()
        ->isCancelled()->toBeFalse();
});

it('casts status to enum', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Test',
        'token' => hash('sha256', 'enum-test'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect($invite->status)->toBe(InvitationStatus::Accepted);
});

it('casts metadata to array', function () {
    $metadata = ['role' => 'admin', 'department' => 'engineering'];

    $invite = $this->user->invitations()->create([
        'name' => 'Metadata Test',
        'token' => hash('sha256', 'metadata-test'),
        'status' => InvitationStatus::Pending,
        'metadata' => $metadata,
    ]);

    expect($invite->fresh()->metadata)->toBe($metadata);
});

it('detects expired invitation by timestamp', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Expired Test',
        'token' => hash('sha256', 'expired-test'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    expect($invite->isExpired())->toBeTrue();
});

it('detects expired invitation by status', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Expired Test',
        'token' => hash('sha256', 'expired-status'),
        'status' => InvitationStatus::Expired,
    ]);

    expect($invite->isExpired())->toBeTrue();
});

it('detects declined invitation', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Declined Test',
        'token' => hash('sha256', 'declined-test'),
        'status' => InvitationStatus::Declined,
    ]);

    expect($invite->isDeclined())->toBeTrue();
});

it('detects cancelled invitation', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Cancelled Test',
        'token' => hash('sha256', 'cancelled-test'),
        'status' => InvitationStatus::Cancelled,
    ]);

    expect($invite->isCancelled())->toBeTrue();
});

it('scopes pending invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Pending',
        'token' => hash('sha256', 'pending-scope'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => hash('sha256', 'accepted-scope'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect(Invite::pending()->count())->toBe(1);
    expect(Invite::accepted()->count())->toBe(1);
});

it('scopes declined and cancelled invitations', function () {
    $this->user->invitations()->create([
        'name' => 'Declined',
        'token' => hash('sha256', 'declined-scope'),
        'status' => InvitationStatus::Declined,
    ]);

    $this->user->invitations()->create([
        'name' => 'Cancelled',
        'token' => hash('sha256', 'cancelled-scope'),
        'status' => InvitationStatus::Cancelled,
    ]);

    expect(Invite::declined()->count())->toBe(1);
    expect(Invite::cancelled()->count())->toBe(1);
});

it('scopes active invitations', function () {
    $activeToken = hash('sha256', 'active-token');
    $expiredToken = hash('sha256', 'expired-token');

    $this->user->invitations()->create([
        'name' => 'Active',
        'token' => $activeToken,
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $this->user->invitations()->create([
        'name' => 'Past Expiry',
        'token' => $expiredToken,
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    expect(Invite::active()->count())->toBe(1);
    expect(Invite::forToken($activeToken)->active()->exists())->toBeTrue();
    expect(Invite::forToken($expiredToken)->active()->exists())->toBeFalse();
});

it('scopes by token', function () {
    $hashedToken = hash('sha256', 'find-me-token');

    $this->user->invitations()->create([
        'name' => 'Find Me',
        'token' => $hashedToken,
        'status' => InvitationStatus::Pending,
    ]);

    expect(Invite::forToken($hashedToken)->first())->not->toBeNull();
    expect(Invite::forToken('nonexistent')->first())->toBeNull();
});

it('stores accepted_by and accepted_ip', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Audit Test',
        'token' => hash('sha256', 'audit-test'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
        'accepted_by' => 42,
        'accepted_ip' => '192.168.1.1',
    ]);

    $invite = $invite->fresh();
    expect($invite->accepted_by)->toBe(42);
    expect($invite->accepted_ip)->toBe('192.168.1.1');
});

it('uses explicit fillable instead of guarded', function () {
    $invite = new Invite;

    expect($invite->getFillable())->not->toBeEmpty();
    expect($invite->getGuarded())->toBe(['*']);
});
