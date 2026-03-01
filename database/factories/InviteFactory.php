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
        $token = Str::random(64);

        return [
            'name' => fake()->sentence(3),
            'token' => hash('sha256', $token),
            'status' => InvitationStatus::Pending,
            'invited_by' => null,
            'metadata' => null,
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

    public function declined(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Declined,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Cancelled,
        ]);
    }

    /**
     * Create with a known plaintext token (for testing lookups).
     */
    public function withPlainToken(string $plainToken): static
    {
        return $this->state(fn () => [
            'token' => hash('sha256', $plainToken),
        ]);
    }
}
