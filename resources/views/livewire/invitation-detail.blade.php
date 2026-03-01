<div>
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ $invite->name }}</flux:heading>
        <flux:button href="{{ route('inviteable.dashboard') }}" variant="ghost">Back</flux:button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <flux:card>
            <flux:heading size="sm" class="mb-4">Details</flux:heading>

            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500">Status</dt>
                    <dd><flux:badge color="{{ $invite->status->color() }}">{{ $invite->status->label() }}</flux:badge></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Inviteable</dt>
                    <dd>{{ class_basename($invite->inviteable_type) }} #{{ $invite->inviteable_id }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Invited By</dt>
                    <dd>{{ $invite->invited_by ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Expires</dt>
                    <dd>{{ $invite->expired_at?->format('Y-m-d H:i:s') ?? 'Never' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Created</dt>
                    <dd>{{ $invite->created_at->format('Y-m-d H:i:s') }}</dd>
                </div>
            </dl>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4">Audit</flux:heading>

            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500">Accepted By</dt>
                    <dd>{{ $invite->accepted_by ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Accepted IP</dt>
                    <dd>{{ $invite->accepted_ip ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Accepted At</dt>
                    <dd>{{ $invite->accepted_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</dd>
                </div>
            </dl>
        </flux:card>

        @if ($invite->metadata)
            <flux:card class="md:col-span-2">
                <flux:heading size="sm" class="mb-4">Metadata</flux:heading>

                <dl class="space-y-2">
                    @foreach ($invite->metadata as $key => $value)
                        <div class="flex">
                            <dt class="text-sm text-gray-500 w-1/3">{{ $key }}</dt>
                            <dd class="text-sm">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </flux:card>
        @endif
    </div>

    <div class="flex items-center gap-2 mt-6">
        @if ($invite->isPending())
            <flux:button wire:click="resend" variant="primary">Resend</flux:button>
            <flux:button wire:click="revoke" variant="danger">Revoke</flux:button>
        @endif
        <flux:button wire:click="$set('showDeleteModal', true)" variant="danger" variant="ghost">Delete</flux:button>
    </div>

    <flux:modal wire:model="showDeleteModal">
        <flux:heading size="lg">Delete Invitation</flux:heading>
        <p class="mt-2 text-gray-600">Are you sure you want to permanently delete this invitation? This action cannot be undone.</p>
        <div class="flex justify-end gap-2 mt-4">
            <flux:button wire:click="$set('showDeleteModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="delete" variant="danger">Delete</flux:button>
        </div>
    </flux:modal>
</div>
