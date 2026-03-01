<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Events;

use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationDeclined
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Invite $invitation,
    ) {}
}
