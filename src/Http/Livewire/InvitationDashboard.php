<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Livewire;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class InvitationDashboard extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    /** @var array<int, int> */
    public array $selected = [];

    public bool $selectAll = false;

    protected string $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->all();
        } else {
            $this->selected = [];
        }
    }

    public function bulkRevoke(): void
    {
        $manager = app(InviteableManager::class);

        $invites = Invite::query()->whereIn('id', $this->selected)->pending()->get();
        foreach ($invites as $invite) {
            $invite->update(['status' => InvitationStatus::Revoked]);
        }

        $this->selected = [];
        $this->selectAll = false;

        session()->flash('message', 'Selected invitations have been revoked.');
    }

    public function bulkDelete(): void
    {
        Invite::query()->whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->selectAll = false;

        session()->flash('message', 'Selected invitations have been deleted.');
    }

    public function resend(int $id): void
    {
        $invite = Invite::findOrFail($id);

        app(InviteableManager::class)->resend($invite);

        session()->flash('message', 'Invitation has been resent.');
    }

    public function render(): View
    {
        return view('inviteable::livewire.invitation-dashboard', [
            'invitations' => $this->getQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(15),
            'statuses' => InvitationStatus::cases(),
        ]);
    }

    private function getQuery(): Builder
    {
        $query = Invite::query();

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter !== '') {
            $status = InvitationStatus::tryFrom($this->statusFilter);
            if ($status) {
                $query->where('status', $status);
            }
        }

        return $query;
    }
}
