<?php

namespace CleaniqueCoders\Inviteable\Tests\Stubs;

use CleaniqueCoders\Inviteable\Traits\HasInviteable;
use Illuminate\Foundation\Auth\User as Eloquent;

class User extends Eloquent
{
    use HasInviteable;

    protected $guarded = ['id'];
}
