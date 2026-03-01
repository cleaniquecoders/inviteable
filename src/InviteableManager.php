<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable;

use Carbon\Carbon;
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InviteableManager
{
    /**
     * @param  Model&\CleaniqueCoders\Inviteable\Concerns\HasInviteable  $inviteable
     */
    public function create(
        Model $inviteable,
        string $name,
        ?int $invitedBy = null,
        ?int $expiryHours = null,
    ): Invite {
        $tokenLength = (int) config('inviteable.token.length', 64);
        $expiryHours ??= (int) config('inviteable.expiry.duration', 48);

        /** @var Invite */
        return $inviteable->invitations()->create([
            'name' => $name,
            'token' => Str::random($tokenLength),
            'status' => InvitationStatus::Pending,
            'invited_by' => $invitedBy,
            'expired_at' => Carbon::now()->addHours($expiryHours),
        ]);
    }

    public function accept(string $token): Invite
    {
        $invite = Invite::query()->forToken($token)->active()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Accepted,
            'accepted_at' => Carbon::now(),
        ]);

        return $invite->fresh();
    }

    public function revoke(string $token): Invite
    {
        $invite = Invite::query()->forToken($token)->pending()->firstOrFail();

        $invite->update([
            'status' => InvitationStatus::Revoked,
        ]);

        return $invite->fresh();
    }

    public function findByToken(string $token): ?Invite
    {
        return Invite::query()->forToken($token)->first();
    }
}
