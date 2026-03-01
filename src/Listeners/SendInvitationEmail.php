<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Listeners;

use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmail
{
    public function handle(InvitationCreated $event): void
    {
        $invitation = $event->invitation;
        $inviteable = $invitation->inviteable;

        if ($inviteable && isset($inviteable->email)) {
            Mail::to($inviteable)->send(new InvitationMail($invitation->token));
        }
    }
}
