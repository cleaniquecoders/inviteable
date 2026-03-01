<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Mail\InvitationMail;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Mail;

it('sends invitation email when invitation is created via manager', function () {
    Mail::fake();

    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $manager = app(InviteableManager::class);
    $invite = $manager->create($user, 'Email Test');

    expect($invite->plainToken)->not->toBeNull();

    Mail::assertQueued(InvitationMail::class, function (InvitationMail $mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('queues mail by default', function () {
    Mail::fake();

    $user = User::create([
        'name' => 'Test User',
        'email' => 'queue@example.com',
        'password' => bcrypt('password'),
    ]);

    $manager = app(InviteableManager::class);
    $manager->create($user, 'Queue Test');

    Mail::assertQueued(InvitationMail::class);
});

it('sends mail synchronously when queue disabled', function () {
    config(['inviteable.mail.queue' => false]);
    Mail::fake();

    $user = User::create([
        'name' => 'Test User',
        'email' => 'sync@example.com',
        'password' => bcrypt('password'),
    ]);

    $manager = app(InviteableManager::class);
    $manager->create($user, 'Sync Test');

    Mail::assertSent(InvitationMail::class);
});

it('constructs mailable with token', function () {
    $token = 'test-plain-token';
    $mail = new InvitationMail($token);

    expect($mail->token)->toBe($token);
});
