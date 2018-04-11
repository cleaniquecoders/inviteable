<?php

namespace CleaniqueCoders\Inviteable\Tests;

use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
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

        User::create([
            'email'    => 'admin@testbench.com',
            'name'     => 'Admin Test Bench',
            'password' => bcrypt('secret'),
        ]);

        $invitation = User::create([
            'email'    => 'test@testbench.com',
            'name'     => 'Test Bench',
            'password' => bcrypt('secret'),
        ])
            ->invitations()
            ->create([
                'name'       => 'Invitation',
                'token'      => Str::random(64),
                'invited_by' => User::first()->id,
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
    }
}
