<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Database\Factories;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InviteFactory extends Factory
{
    protected $model = Invite::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'token' => Str::random(64),
            'status' => InvitationStatus::Pending,
            'invited_by' => null,
            'expired_at' => now()->addHours((int) config('inviteable.expiry.duration', 48)),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Accepted,
            'accepted_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Expired,
            'expired_at' => now()->subHour(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Revoked,
        ]);
    }
}
