<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Enums;

enum InvitationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Expired = 'expired';
    case Revoked = 'revoked';
}
