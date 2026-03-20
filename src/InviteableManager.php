<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable;

use Carbon\Carbon;
use CleaniqueCoders\Inviteable\Concerns\HasInviteable;
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationCancelled;
use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Events\InvitationDeclined;
use CleaniqueCoders\Inviteable\Events\InvitationRevoked;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InviteableManager
{
    /**
     * @param  Model&HasInviteable  $inviteable
     * @param  array<string, mixed>|null  $metadata
     */
    public function create(
        Model $inviteable,
        string $name,
        ?int $invitedBy = null,
        ?int $expiryHours = null,
        ?array $metadata = null,
    ): Invite {
        $tokenLength = (int) config('inviteable.token.length', 64);
        $expiryHours ??= (int) config('inviteable.expiry.duration', 48);

        $plainToken = Str::random($tokenLength);
        $hashedToken = hash('sha256', $plainToken);

        /** @var Invite */
        $invite = $inviteable->invitations()->create([
            'name' => $name,
            'token' => $hashedToken,
            'status' => InvitationStatus::Pending,
            'invited_by' => $invitedBy,
            'metadata' => $metadata,
            'expired_at' => Carbon::now()->addHours($expiryHours),
        ]);

        $invite->plainToken = $plainToken;

        return $invite;
    }

    /**
     * @param  Model&HasInviteable  $inviteable
     * @param  array<array{name: string, invited_by?: int|null, expiry_hours?: int|null, metadata?: array<string, mixed>|null}>  $invitations
     * @return Collection<int, Invite>
     */
    public function createBatch(
        Model $inviteable,
        array $invitations,
        ?int $invitedBy = null,
    ): Collection {
        return DB::transaction(function () use ($inviteable, $invitations, $invitedBy): Collection {
            $results = new Collection;

            foreach ($invitations as $data) {
                $results->push($this->create(
                    inviteable: $inviteable,
                    name: $data['name'],
                    invitedBy: $data['invited_by'] ?? $invitedBy,
                    expiryHours: $data['expiry_hours'] ?? null,
                    metadata: $data['metadata'] ?? null,
                ));
            }

            return $results;
        });
    }

    public function accept(string $token): Invite
    {
        $hashedToken = hash('sha256', $token);

        $invite = Invite::query()->forToken($hashedToken)->active()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Accepted,
            'accepted_at' => Carbon::now(),
        ]);

        return $invite->fresh();
    }

    public function decline(string $token): Invite
    {
        $hashedToken = hash('sha256', $token);

        $invite = Invite::query()->forToken($hashedToken)->active()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Declined,
        ]);

        event(new InvitationDeclined($invite->fresh()));

        return $invite->fresh();
    }

    public function revoke(string $token): Invite
    {
        $hashedToken = hash('sha256', $token);

        $invite = Invite::query()->forToken($hashedToken)->pending()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Revoked,
        ]);

        event(new InvitationRevoked($invite->fresh()));

        return $invite->fresh();
    }

    public function cancel(string $token): Invite
    {
        $hashedToken = hash('sha256', $token);

        $invite = Invite::query()->forToken($hashedToken)->pending()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Cancelled,
        ]);

        event(new InvitationCancelled($invite->fresh()));

        return $invite->fresh();
    }

    public function findByToken(string $token): ?Invite
    {
        $hashedToken = hash('sha256', $token);

        return Invite::query()->forToken($hashedToken)->first();
    }

    /**
     * Resend an invitation by regenerating the token and resetting expiry.
     */
    public function resend(string|Invite $invite): Invite
    {
        if (is_string($invite)) {
            $hashedToken = hash('sha256', $invite);
            $invite = Invite::query()->forToken($hashedToken)->firstOrFail();
        }

        $tokenLength = (int) config('inviteable.token.length', 64);
        $expiryHours = (int) config('inviteable.expiry.duration', 48);

        $plainToken = Str::random($tokenLength);
        $hashedToken = hash('sha256', $plainToken);

        $invite->update([
            'token' => $hashedToken,
            'status' => InvitationStatus::Pending,
            'expired_at' => Carbon::now()->addHours($expiryHours),
        ]);

        $invite = $invite->fresh();
        $invite->plainToken = $plainToken;

        // Fire the created event to trigger email sending
        event(new InvitationCreated($invite));

        return $invite;
    }
}
