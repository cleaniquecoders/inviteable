<?php

namespace CleaniqueCoders\Inviteable\Traits;

trait HasInviteable
{
    /**
     * Get all invitations.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function inviteables()
    {
        return $this->morphMany(\CleaniqueCoders\Inviteable\Models\Invite::class, 'inviteable');
    }
}
