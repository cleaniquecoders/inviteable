<div>
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Invitations</flux:heading>
        <flux:button href="{{ route('inviteable.create') }}" variant="primary">
            Create Invitation
        </flux:button>
    </div>

    <div class="flex items-center gap-4 mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by name..." icon="magnifying-glass" class="flex-1" />

        <flux:select wire:model.live="statusFilter" placeholder="All statuses" class="w-48">
            <flux:select.option value="">All statuses</flux:select.option>
            @foreach ($statuses as $status)
                <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @if (count($selected) > 0)
        <div class="flex items-center gap-2 mb-4">
            <flux:badge>{{ count($selected) }} selected</flux:badge>
            <flux:button wire:click="bulkRevoke" variant="danger" size="sm">Revoke Selected</flux:button>
            <flux:button wire:click="bulkDelete" variant="danger" size="sm" wire:confirm="Are you sure you want to delete the selected invitations?">Delete Selected</flux:button>
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <flux:checkbox wire:model.live="selectAll" />
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'status'" :direction="$sortDirection" wire:click="sortBy('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'expired_at'" :direction="$sortDirection" wire:click="sortBy('expired_at')">Expires</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($invitations as $invitation)
                <flux:table.row :key="$invitation->id">
                    <flux:table.cell>
                        <flux:checkbox wire:model.live="selected" value="{{ $invitation->id }}" />
                    </flux:table.cell>
                    <flux:table.cell>
                        <a href="{{ route('inviteable.detail', $invitation) }}" class="text-blue-600 hover:underline">
                            {{ $invitation->name }}
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $invitation->status->color() }}">{{ $invitation->status->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $invitation->expired_at?->diffForHumans() ?? 'Never' }}</flux:table.cell>
                    <flux:table.cell>{{ $invitation->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            @if ($invitation->isPending())
                                <flux:button wire:click="resend({{ $invitation->id }})" size="xs" variant="ghost">Resend</flux:button>
                            @endif
                            <flux:button href="{{ route('inviteable.detail', $invitation) }}" size="xs" variant="ghost">View</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-gray-500 py-8">
                        No invitations found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $invitations->links() }}
    </div>
</div>
