<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Concerns;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasInviteable
{
    public function invitations(): MorphMany
    {
        return $this->morphMany(Invite::class, 'inviteable');
    }

    public function pendingInvitations(): MorphMany
    {
        return $this->invitations()->where('status', InvitationStatus::Pending);
    }

    public function acceptedInvitations(): MorphMany
    {
        return $this->invitations()->where('status', InvitationStatus::Accepted);
    }

    public function declinedInvitations(): MorphMany
    {
        return $this->invitations()->where('status', InvitationStatus::Declined);
    }

    public function revokedInvitations(): MorphMany
    {
        return $this->invitations()->where('status', InvitationStatus::Revoked);
    }
}
