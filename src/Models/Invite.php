<?php

namespace CleaniqueCoders\Inviteable\Models;

use CleaniqueCoders\Inviteable\Events\InvitationCreated;
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
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => InvitationCreated::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expired_at', 'deleted_at', 'created_at', 'updated_at',
    ];

    /**
     * Get all of the owning invitations models.
     */
    public function inviteables()
    {
        return $this->morphTo();
    }

    /**
     * Get Active Invitation(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $token Invitation Token
     *
     * @return bool
     */
    public function scopeActiveInvitation($query, $token = null)
    {
        if (! is_null($token)) {
            $query->where('token', $token);
        }

        return $query->where('is_expired', false);
    }

    /**
     * Get Expired Invitation(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $token Invitation Token
     *
     * @return bool
     */
    public function scopeExpiredInvitation($query, $token = null)
    {
        if (! is_null($token)) {
            $query->where('token', $token);
        }

        return $query->where('is_expired', true);
    }

    /**
     * Get Accepted Invitation(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $token Invitation Token
     *
     * @return bool
     */
    public function scopeAcceptedInvitation($query, $token = null)
    {
        if (! is_null($token)) {
            $query->where('token', $token);
        }

        return $query->where('is_accepted', true);
    }
}
