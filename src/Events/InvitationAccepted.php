<?php

namespace CleaniqueCoders\Inviteable\Events;

use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted
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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
