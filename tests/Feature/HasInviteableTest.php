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

it('has invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Test',
        'token' => hash('sha256', 'relationship-test'),
        'status' => InvitationStatus::Pending,
    ]);

    expect($this->user->invitations)->toHaveCount(1);
    expect($this->user->invitations->first())->toBeInstanceOf(Invite::class);
});

it('has pending invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Pending',
        'token' => hash('sha256', 'pending-rel'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => hash('sha256', 'accepted-rel'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect($this->user->pendingInvitations)->toHaveCount(1);
    expect($this->user->pendingInvitations->first()->name)->toBe('Pending');
});

it('has accepted invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Pending',
        'token' => hash('sha256', 'pending-acc'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted',
        'token' => hash('sha256', 'accepted-acc'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    expect($this->user->acceptedInvitations)->toHaveCount(1);
    expect($this->user->acceptedInvitations->first()->name)->toBe('Accepted');
});

it('has declined invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Declined',
        'token' => hash('sha256', 'declined-rel'),
        'status' => InvitationStatus::Declined,
    ]);

    expect($this->user->declinedInvitations)->toHaveCount(1);
});

it('has revoked invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Revoked',
        'token' => hash('sha256', 'revoked-rel'),
        'status' => InvitationStatus::Revoked,
    ]);

    expect($this->user->revokedInvitations)->toHaveCount(1);
});

it('resolves morph relationship back to user', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Morph Test',
        'token' => hash('sha256', 'morph-test'),
        'status' => InvitationStatus::Pending,
    ]);

    expect($invite->inviteable)->toBeInstanceOf(User::class);
    expect($invite->inviteable->id)->toBe($this->user->id);
});
