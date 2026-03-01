<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Http\Livewire\AcceptDeclineInvitation;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    Event::fake();
    Mail::fake();

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

it('renders the accept/decline component for pending invitation', function () {
    $plainToken = Str::random(64);

    $invite = $this->user->invitations()->create([
        'name' => 'Accept/Decline Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    Livewire::test(AcceptDeclineInvitation::class, [
        'invitation' => $invite,
        'token' => $plainToken,
    ])
        ->assertOk()
        ->assertSee('Accept/Decline Test');
});

it('accepts an invitation', function () {
    $plainToken = Str::random(64);

    $invite = $this->user->invitations()->create([
        'name' => 'Accept Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    Livewire::test(AcceptDeclineInvitation::class, [
        'invitation' => $invite,
        'token' => $plainToken,
    ])
        ->call('accept')
        ->assertSet('resultMessage', 'Invitation accepted successfully.');

    expect($invite->fresh()->status)->toBe(InvitationStatus::Accepted);
});

it('declines an invitation', function () {
    $plainToken = Str::random(64);

    $invite = $this->user->invitations()->create([
        'name' => 'Decline Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    Livewire::test(AcceptDeclineInvitation::class, [
        'invitation' => $invite,
        'token' => $plainToken,
    ])
        ->call('decline')
        ->assertSet('resultMessage', 'Invitation declined.');

    expect($invite->fresh()->status)->toBe(InvitationStatus::Declined);
});

it('shows already accepted state', function () {
    $plainToken = Str::random(64);

    $invite = $this->user->invitations()->create([
        'name' => 'Already Accepted',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    Livewire::test(AcceptDeclineInvitation::class, [
        'invitation' => $invite,
        'token' => $plainToken,
    ])
        ->assertSee('Already Accepted');
});
