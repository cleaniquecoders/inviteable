<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Models;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $name
 * @property string $token
 * @property InvitationStatus $status
 * @property int|null $invited_by
 * @property int $inviteable_id
 * @property string $inviteable_type
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Invite extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => InvitationCreated::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => InvitationStatus::class,
            'accepted_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function inviteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InvitationStatus::Pending);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', InvitationStatus::Accepted);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', InvitationStatus::Expired);
    }

    public function scopeRevoked(Builder $query): Builder
    {
        return $query->where('status', InvitationStatus::Revoked);
    }

    public function scopeForToken(Builder $query, string $token): Builder
    {
        return $query->where('token', $token);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', InvitationStatus::Pending)
            ->where(function (Builder $query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            });
    }

    public function isPending(): bool
    {
        return $this->status === InvitationStatus::Pending;
    }

    public function isAccepted(): bool
    {
        return $this->status === InvitationStatus::Accepted;
    }

    public function isExpired(): bool
    {
        if ($this->status === InvitationStatus::Expired) {
            return true;
        }

        return $this->isPending() && $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->status === InvitationStatus::Revoked;
    }

    protected static function newFactory(): \CleaniqueCoders\Inviteable\Database\Factories\InviteFactory
    {
        return \CleaniqueCoders\Inviteable\Database\Factories\InviteFactory::new();
    }
}
