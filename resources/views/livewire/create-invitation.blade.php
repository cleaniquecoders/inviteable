<div>
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Create Invitation</flux:heading>
        <flux:button href="{{ route('inviteable.dashboard') }}" variant="ghost">Back</flux:button>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:select wire:model="inviteableType" label="Inviteable Type" placeholder="Select a type">
                @foreach ($inviteableTypes as $label => $class)
                    <flux:select.option value="{{ $class }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            @error('inviteableType') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

            <flux:input wire:model="inviteableId" label="Inviteable ID" type="number" placeholder="Enter model ID" />
            @error('inviteableId') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

            <flux:switch wire:model.live="batchMode" label="Batch mode" description="Create multiple invitations at once" />

            @if ($batchMode)
                <flux:textarea wire:model="batchNames" label="Names (one per line)" rows="5" placeholder="John Doe&#10;Jane Smith&#10;Bob Wilson" />
                @error('batchNames') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @else
                <flux:input wire:model="name" label="Name" placeholder="Enter invitation name" />
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @endif

            <flux:input wire:model="expiryHours" label="Expiry (hours)" type="number" min="1" />

            <div>
                <div class="flex items-center justify-between mb-2">
                    <flux:heading size="sm">Metadata</flux:heading>
                    <flux:button wire:click="addMetadataField" size="xs" variant="ghost">Add Field</flux:button>
                </div>

                @foreach ($metadataFields as $index => $field)
                    <div class="flex items-center gap-2 mb-2">
                        <flux:input wire:model="metadataFields.{{ $index }}.key" placeholder="Key" class="flex-1" />
                        <flux:input wire:model="metadataFields.{{ $index }}.value" placeholder="Value" class="flex-1" />
                        <flux:button wire:click="removeMetadataField({{ $index }})" size="xs" variant="ghost" icon="x-mark" />
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $batchMode ? 'Create Invitations' : 'Create Invitation' }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
