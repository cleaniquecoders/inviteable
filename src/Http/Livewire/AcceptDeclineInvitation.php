<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Livewire;

use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AcceptDeclineInvitation extends Component
{
    public Invite $invitation;

    public string $token;

    public string $resultMessage = '';

    public function mount(Invite $invitation, string $token): void
    {
        $this->invitation = $invitation;
        $this->token = $token;
    }

    public function accept(): void
    {
        $manager = app(InviteableManager::class);

        try {
            $manager->accept($this->token);
            $this->invitation->refresh();
            $this->resultMessage = 'Invitation accepted successfully.';
        } catch (\Exception $e) {
            $this->resultMessage = 'Unable to accept this invitation.';
        }
    }

    public function decline(): void
    {
        $manager = app(InviteableManager::class);

        try {
            $manager->decline($this->token);
            $this->invitation->refresh();
            $this->resultMessage = 'Invitation declined.';
        } catch (\Exception $e) {
            $this->resultMessage = 'Unable to decline this invitation.';
        }
    }

    public function render(): View
    {
        return view('inviteable::livewire.accept-decline-invitation');
    }
}
