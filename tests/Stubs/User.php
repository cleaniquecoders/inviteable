<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Tests\Stubs;

use CleaniqueCoders\Inviteable\Concerns\HasInviteable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasInviteable;

    protected $guarded = [];
}
