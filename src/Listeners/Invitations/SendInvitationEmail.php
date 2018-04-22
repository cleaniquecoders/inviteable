<?php

namespace CleaniqueCoders\Inviteable\Listeners\Invitations;

use CleaniqueCoders\Inviteable\Mail\SendInvitationMail;
use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InvitationCreated  $event
     * @return void
     */
    public function handle(InvitationCreated $event)
    {
        $invitation = $event->invitation;

        $object = $invitation->inviteable_type::where('id', $invitation->inviteable_id)->first();
        if($object && isset($object->email)) {
            Mail::to($object)->send(new SendInvitationMail($invitation->token));
        }
    }
}
