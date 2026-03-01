<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Event::fake();
    Mail::fake();

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->manager = app(InviteableManager::class);
});

it('creates invitation with hashed token', function () {
    $invite = $this->manager->create($this->user, 'Test Invite');

    expect($invite->plainToken)->not->toBeNull();
    expect($invite->plainToken)->not->toBe($invite->token);
    expect(hash('sha256', $invite->plainToken))->toBe($invite->token);
});

it('creates invitation with metadata', function () {
    $metadata = ['role' => 'editor', 'level' => 3];

    $invite = $this->manager->create(
        $this->user,
        'Metadata Test',
        metadata: $metadata,
    );

    expect($invite->fresh()->metadata)->toBe($metadata);
});

it('creates invitation with custom expiry', function () {
    $invite = $this->manager->create($this->user, 'Custom Expiry', expiryHours: 72);

    expect($invite->expired_at)->not->toBeNull();
    expect($invite->expired_at->diffInHours(now(), true))->toBeLessThan(73);
    expect($invite->expired_at->diffInHours(now(), true))->toBeGreaterThan(71);
});

it('accepts an invitation', function () {
    $invite = $this->manager->create($this->user, 'Accept Test');
    $plainToken = $invite->plainToken;

    $accepted = $this->manager->accept($plainToken);

    expect($accepted->status)->toBe(InvitationStatus::Accepted);
    expect($accepted->accepted_at)->not->toBeNull();
});

it('declines an invitation', function () {
    $invite = $this->manager->create($this->user, 'Decline Test');
    $plainToken = $invite->plainToken;

    $declined = $this->manager->decline($plainToken);

    expect($declined->status)->toBe(InvitationStatus::Declined);
});

it('revokes an invitation', function () {
    $invite = $this->manager->create($this->user, 'Revoke Test');
    $plainToken = $invite->plainToken;

    $revoked = $this->manager->revoke($plainToken);

    expect($revoked->status)->toBe(InvitationStatus::Revoked);
});

it('cancels an invitation', function () {
    $invite = $this->manager->create($this->user, 'Cancel Test');
    $plainToken = $invite->plainToken;

    $cancelled = $this->manager->cancel($plainToken);

    expect($cancelled->status)->toBe(InvitationStatus::Cancelled);
});

it('finds invitation by token', function () {
    $invite = $this->manager->create($this->user, 'Find Test');
    $plainToken = $invite->plainToken;

    $found = $this->manager->findByToken($plainToken);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($invite->id);
});

it('returns null for unknown token', function () {
    $found = $this->manager->findByToken('nonexistent-token');

    expect($found)->toBeNull();
});

it('creates batch invitations', function () {
    $invitations = [
        ['name' => 'Alice'],
        ['name' => 'Bob'],
        ['name' => 'Charlie'],
    ];

    $results = $this->manager->createBatch($this->user, $invitations, invitedBy: 1);

    expect($results)->toHaveCount(3);
    expect($results->pluck('name')->all())->toBe(['Alice', 'Bob', 'Charlie']);

    foreach ($results as $invite) {
        expect($invite->plainToken)->not->toBeNull();
        expect($invite->invited_by)->toBe(1);
        expect($invite->status)->toBe(InvitationStatus::Pending);
    }
});

it('creates batch invitations with individual metadata', function () {
    $invitations = [
        ['name' => 'Alice', 'metadata' => ['role' => 'admin']],
        ['name' => 'Bob', 'metadata' => ['role' => 'editor']],
    ];

    $results = $this->manager->createBatch($this->user, $invitations);

    expect($results[0]->fresh()->metadata)->toBe(['role' => 'admin']);
    expect($results[1]->fresh()->metadata)->toBe(['role' => 'editor']);
});

it('resends an invitation by model', function () {
    $invite = $this->manager->create($this->user, 'Resend Test');
    $originalToken = $invite->token;

    $resent = $this->manager->resend($invite);

    expect($resent->token)->not->toBe($originalToken);
    expect($resent->plainToken)->not->toBeNull();
    expect($resent->status)->toBe(InvitationStatus::Pending);
});

it('resends an invitation by token', function () {
    $invite = $this->manager->create($this->user, 'Resend Token Test');
    $plainToken = $invite->plainToken;
    $originalToken = $invite->token;

    $resent = $this->manager->resend($plainToken);

    expect($resent->token)->not->toBe($originalToken);
    expect($resent->status)->toBe(InvitationStatus::Pending);
});
