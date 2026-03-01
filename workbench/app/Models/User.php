<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use CleaniqueCoders\Inviteable\Concerns\HasInviteable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasInviteable;

    protected $guarded = [];
}
