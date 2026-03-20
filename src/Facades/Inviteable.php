<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Facades;

use CleaniqueCoders\Inviteable\InviteableManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \CleaniqueCoders\Inviteable\Models\Invite create(\Illuminate\Database\Eloquent\Model $inviteable, string $name, ?int $invitedBy = null, ?int $expiryHours = null, ?array $metadata = null)
 * @method static \Illuminate\Support\Collection createBatch(\Illuminate\Database\Eloquent\Model $inviteable, array $invitations, ?int $invitedBy = null)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite accept(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite decline(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite revoke(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite cancel(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite|null findByToken(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite resend(string|\CleaniqueCoders\Inviteable\Models\Invite $invite)
 *
 * @see InviteableManager
 */
class Inviteable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InviteableManager::class;
    }
}
