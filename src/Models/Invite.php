<?php

namespace CleaniqueCoders\Inviteable\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expired_at', 'deleted_at', 'created_at', 'updated_at',
    ];
}
