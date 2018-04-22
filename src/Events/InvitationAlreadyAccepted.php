<?php

namespace CleaniqueCoders\Inviteable\Events;

use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvitationAlreadyAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Invite object
     * 
     * @var CleaniqueCoders\Inviteable\Models\Invite
     */
    public $invitation;

    /**
     * Create a new event instance.
     *
     * @return void
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
