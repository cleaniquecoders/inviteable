<?php

namespace CleaniqueCoders\Inviteable\Tests;

use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Exceptions\InvalidInvitationToken;
use CleaniqueCoders\Inviteable\Listeners\Invitations\SendInvitationMail as SendInvitationMailListener;
use CleaniqueCoders\Inviteable\Mail\SendInvitationMail;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InviteTest extends TestCase
{
    /** @test */
    public function it_has_users_table()
    {
        $this->assertTrue(Schema::hasTable('users'));
    }

    /** @test */
    public function it_has_invites_table()
    {
        $this->assertTrue(Schema::hasTable('invites'));
    }

    /** @test */
    public function it_can_generate_invitation()
    {
        Event::fake();
        Mail::fake();

        $admin = User::create([
            'email'    => 'admin@testbench.com',
            'name'     => 'Admin Test Bench',
            'password' => bcrypt('secret'),
        ]);

        $user = User::create([
            'email'    => 'test@testbench.com',
            'name'     => 'Test Bench',
            'password' => bcrypt('secret'),
        ]);

        $invitation = $user
            ->invitations()
            ->create([
                'name'       => 'Invitation',
                'token'      => Str::random(64),
                'invited_by' => $admin->id,
                'is_expired' => false,
                'expired_at' => \Carbon\Carbon::now()->addHours(24),
            ]);
        
        Event::assertDispatched(InvitationCreated::class, function ($event) use ($invitation) {
            return $event->invitation->id === $invitation->id;
        });
        
        $this->assertEquals('CleaniqueCoders\Inviteable\Tests\Stubs\User', $invitation->inviteable_type);
        $this->assertEquals(2, $invitation->inviteable_id);
        $this->assertEquals(1, $invitation->invited_by);
        $this->assertEquals('Invitation', $invitation->name);

        /**
         * E-mail Invitation Test
         */
        Mail::to($user)->send(new SendInvitationMail($invitation->token));
        
        Mail::assertSent(SendInvitationMail::class, function ($mail) use ($invitation) {
            return $mail->token === $invitation->token;
        });

        Mail::assertSent(SendInvitationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $response = $this->get('invitation/' . $invitation->token);
        $response->assertStatus(302);
        
        /**
         * First time navigate to invitation/{token}
         */
        Event::assertDispatched(InvitationAccepted::class, function ($event) use ($invitation) {
            return $event->invitation->id === $invitation->id;
        });

        /**
         * Second time navigate to invitation/{token}
         */
        $response = $this->get('invitation/' . $invitation->token);
        $response->assertStatus(302);
        Event::assertDispatched(InvitationAlreadyAccepted::class, function ($event) use ($invitation) {
            return $event->invitation->id === $invitation->id;
        });

        /**
         * Invalid Token
         * 
         * It should assert expect to throw an exception of invalid invitation token and status code
         */
        $this->expectException(InvalidInvitationToken::class);
        $this->withoutExceptionHandling();
        $response = $this->get('invitation/invalid-token');
        $response->assertStatus(500);
        $this->assertTrue($response->exception instanceof InvalidInvitationToken);
    }
}
