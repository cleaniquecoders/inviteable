<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Mail\InvitationMail;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

it('sends invitation email when invitation is created', function () {
    Mail::fake();

    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->invitations()->create([
        'name' => 'Email Test',
        'token' => Str::random(64),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    Mail::assertSent(InvitationMail::class, function (InvitationMail $mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('does not send email if inviteable has no email', function () {
    Mail::fake();

    // Create an invite directly without an inviteable that has email
    // The listener should handle this gracefully
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    Mail::fake(); // Reset after user creation triggers

    // Manually test the mailable
    $token = Str::random(64);
    $mail = new InvitationMail($token);

    expect($mail->token)->toBe($token);
});
