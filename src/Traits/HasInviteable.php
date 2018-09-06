<?php

namespace CleaniqueCoders\Inviteable\Traits;

/**
 * HasInviteable Trait.
 */
trait HasInviteable
{
    /**
     * Get all invitations.
     */
    public function inviteables()
    {
        return $this->morphMany(\CleaniqueCoders\Inviteable\Models\Invite::class, 'inviteable');
    }
}
