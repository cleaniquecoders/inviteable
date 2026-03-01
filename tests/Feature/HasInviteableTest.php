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

it('has invitations relationship', function () {
    $this->user->invitations()->create([
        'name' => 'Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
    ]);

    expect($this->user->invitations)->toHaveCount(1);
    expect($this->user->invitations->first())->toBeInstanceOf(Invite::class);
});

it('has pending invitations relationship', function () {
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

    expect($this->user->pendingInvitations)->toHaveCount(1);
    expect($this->user->pendingInvitations->first()->name)->toBe('Pending');
});

it('has accepted invitations relationship', function () {
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

    expect($this->user->acceptedInvitations)->toHaveCount(1);
    expect($this->user->acceptedInvitations->first()->name)->toBe('Accepted');
});

it('resolves morph relationship back to user', function () {
    $invite = $this->user->invitations()->create([
        'name' => 'Morph Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
    ]);

    expect($invite->inviteable)->toBeInstanceOf(User::class);
    expect($invite->inviteable->id)->toBe($this->user->id);
});
