<?php

namespace CleaniqueCoders\Inviteable\Events;

use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Invite object.
     *
     * @var CleaniqueCoders\Inviteable\Models\Invite
     */
    public $invitation;

    /**
     * Create a new event instance.
     */
    public function __construct(Invite $invitation)
    {
        $this->invitation = $invitation;
    }
}
