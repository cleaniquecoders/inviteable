<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class InvitationSettings extends Component
{
    public function render(): View
    {
        return view('inviteable::livewire.invitation-settings', [
            'config' => [
                'token_length' => config('inviteable.token.length'),
                'expiry_duration' => config('inviteable.expiry.duration'),
                'rate_limit_attempts' => config('inviteable.rate_limit.max_attempts'),
                'rate_limit_decay' => config('inviteable.rate_limit.decay_minutes'),
                'auth_required' => config('inviteable.auth.required'),
                'auth_guard' => config('inviteable.auth.guard'),
                'mail_queue' => config('inviteable.mail.queue'),
                'api_enabled' => config('inviteable.routes.api'),
                'ui_enabled' => config('inviteable.ui.enabled'),
                'ui_prefix' => config('inviteable.ui.prefix'),
            ],
        ]);
    }
}
