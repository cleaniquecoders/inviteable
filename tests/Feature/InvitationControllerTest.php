<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Exceptions\InvalidInvitationTokenException;
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

it('accepts a pending invitation', function () {
    $token = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => $token,
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $response = $this->get("invitation/{$token}");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationAccepted::class);

    $invite = Invite::forToken($token)->first();
    expect($invite->status)->toBe(InvitationStatus::Accepted);
    expect($invite->accepted_at)->not->toBeNull();
});

it('handles already accepted invitation', function () {
    $token = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test',
        'token' => $token,
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    $response = $this->get("invitation/{$token}");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationAlreadyAccepted::class);
});

it('throws exception for invalid token', function () {
    $this->withoutExceptionHandling();

    $this->get('invitation/invalidtoken123');
})->throws(InvalidInvitationTokenException::class);

it('redirects expired invitations', function () {
    $token = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Expired',
        'token' => $token,
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    $response = $this->get("invitation/{$token}");

    $response->assertRedirect(route('invitation.index'));
});

it('redirects revoked invitations', function () {
    $token = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Revoked',
        'token' => $token,
        'status' => InvitationStatus::Revoked,
    ]);

    $response = $this->get("invitation/{$token}");

    $response->assertRedirect(route('invitation.index'));
});
