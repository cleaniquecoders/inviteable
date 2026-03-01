<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Facades;

use CleaniqueCoders\Inviteable\InviteableManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \CleaniqueCoders\Inviteable\Models\Invite create(\Illuminate\Database\Eloquent\Model $inviteable, string $name, ?int $invitedBy = null, ?int $expiryHours = null)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite accept(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite revoke(string $token)
 * @method static \CleaniqueCoders\Inviteable\Models\Invite|null findByToken(string $token)
 *
 * @see \CleaniqueCoders\Inviteable\InviteableManager
 */
class Inviteable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InviteableManager::class;
    }
}
