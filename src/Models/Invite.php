<?php

namespace CleaniqueCoders\Inviteable\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'expired_at', 'deleted_at', 'created_at', 'updated_at',
    ];
}
