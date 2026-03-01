<?php

declare(strict_types=1);

namespace Workbench\App\Database\Seeders;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Facades\Inviteable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ],
        );

        // Create some sample invitations
        Inviteable::create($user, 'Team Meeting Invite', invitedBy: $user->id);
        Inviteable::create($user, 'Project Onboarding', invitedBy: $user->id, metadata: ['role' => 'developer']);
        Inviteable::create($user, 'Workshop Registration', invitedBy: $user->id, expiryHours: 72);

        // Create an accepted one
        $accepted = Inviteable::create($user, 'Already Accepted Invite');
        Inviteable::accept($accepted->plainToken);

        // Create an expired one
        $user->invitations()->create([
            'name' => 'Expired Conference Invite',
            'token' => hash('sha256', Str::random(64)),
            'status' => InvitationStatus::Expired,
            'invited_by' => $user->id,
            'expired_at' => now()->subDay(),
        ]);
    }
}
