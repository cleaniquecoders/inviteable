<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationDeclined;
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

it('shows confirmation page for valid pending invitation', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $response = $this->get("invitation/{$plainToken}");

    $response->assertOk();
    $response->assertViewIs('inviteable::invitations.confirm');
    $response->assertSee('Test Invitation');
});

it('accepts a pending invitation via POST', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $response = $this->post("invitation/{$plainToken}/accept");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationAccepted::class);

    $invite = Invite::forToken(hash('sha256', $plainToken))->first();
    expect($invite->status)->toBe(InvitationStatus::Accepted);
    expect($invite->accepted_at)->not->toBeNull();
});

it('declines a pending invitation via POST', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test Invitation',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $response = $this->post("invitation/{$plainToken}/decline");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationDeclined::class);

    $invite = Invite::forToken(hash('sha256', $plainToken))->first();
    expect($invite->status)->toBe(InvitationStatus::Declined);
});

it('handles already accepted invitation on show', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    $response = $this->get("invitation/{$plainToken}");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationAlreadyAccepted::class);
});

it('handles already accepted invitation on accept', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    $response = $this->post("invitation/{$plainToken}/accept");

    $response->assertRedirect(route('invitation.index'));

    Event::assertDispatched(InvitationAlreadyAccepted::class);
});

it('throws exception for invalid token', function () {
    $this->withoutExceptionHandling();

    $this->get('invitation/invalidtoken123');
})->throws(InvalidInvitationTokenException::class, 'The invitation token is invalid or does not exist.');

it('redirects expired invitations', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Expired',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->subHour(),
    ]);

    $response = $this->get("invitation/{$plainToken}");

    $response->assertRedirect(route('invitation.index'));
});

it('redirects revoked invitations', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Revoked',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Revoked,
    ]);

    $response = $this->get("invitation/{$plainToken}");

    $response->assertRedirect(route('invitation.index'));
});

it('redirects declined invitations', function () {
    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Declined',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Declined,
    ]);

    $response = $this->get("invitation/{$plainToken}");

    $response->assertRedirect(route('invitation.index'));
});

it('records accepted_by and accepted_ip on accept', function () {
    $plainToken = Str::random(64);
    $authUser = User::create([
        'name' => 'Auth User',
        'email' => 'auth@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->user->invitations()->create([
        'name' => 'Audit Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $this->actingAs($authUser)->post("invitation/{$plainToken}/accept");

    $invite = Invite::forToken(hash('sha256', $plainToken))->first();
    expect($invite->accepted_by)->toBe($authUser->id);
    expect($invite->accepted_ip)->not->toBeNull();
});

it('requires auth when configured', function () {
    config(['inviteable.auth.required' => true]);
    config(['inviteable.auth.redirect' => 'invitation.index']);

    $plainToken = Str::random(64);

    $this->user->invitations()->create([
        'name' => 'Auth Test',
        'token' => hash('sha256', $plainToken),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    $response = $this->post("invitation/{$plainToken}/accept");

    $response->assertRedirect(route('invitation.index'));

    $invite = Invite::forToken(hash('sha256', $plainToken))->first();
    expect($invite->status)->toBe(InvitationStatus::Pending);
});
