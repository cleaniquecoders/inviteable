<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \CleaniqueCoders\Inviteable\Models\Invite
 */
class InviteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'invited_by' => $this->invited_by,
            'accepted_by' => $this->accepted_by,
            'metadata' => $this->metadata,
            'inviteable_type' => $this->inviteable_type,
            'inviteable_id' => $this->inviteable_id,
            'accepted_at' => $this->accepted_at?->toISOString(),
            'expired_at' => $this->expired_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
