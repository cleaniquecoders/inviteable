<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Livewire;

use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class InvitationDetail extends Component
{
    public Invite $invite;

    public bool $showDeleteModal = false;

    public function mount(Invite $invite): void
    {
        $this->invite = $invite;
    }

    public function revoke(): void
    {
        if ($this->invite->isPending()) {
            $this->invite->update(['status' => \CleaniqueCoders\Inviteable\Enums\InvitationStatus::Revoked]);
            $this->invite->refresh();

            session()->flash('message', 'Invitation has been revoked.');
        }
    }

    public function resend(): void
    {
        $manager = app(InviteableManager::class);
        $this->invite = $manager->resend($this->invite);
        $this->invite->refresh();

        session()->flash('message', 'Invitation has been resent.');
    }

    public function delete(): void
    {
        $this->invite->delete();

        session()->flash('message', 'Invitation has been deleted.');

        $this->redirect(route('inviteable.dashboard'));
    }

    public function render(): View
    {
        return view('inviteable::livewire.invitation-detail');
    }
}
