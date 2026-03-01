<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Console;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Console\Command;

class ExpireInvitationsCommand extends Command
{
    protected $signature = 'inviteable:expire';

    protected $description = 'Expire pending invitations that have passed their expiry date';

    public function handle(): int
    {
        $count = Invite::query()
            ->where('status', InvitationStatus::Pending)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->update(['status' => InvitationStatus::Expired->value]);

        $this->info("Expired {$count} invitation(s).");

        return self::SUCCESS;
    }
}
