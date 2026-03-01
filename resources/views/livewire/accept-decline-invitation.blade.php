<div class="flex justify-center items-center min-h-[60vh]">
    <flux:card class="max-w-md w-full text-center">
        @if ($resultMessage)
            <flux:heading size="lg" class="mb-4">{{ $resultMessage }}</flux:heading>
            <flux:badge color="{{ $invitation->status->color() }}">{{ $invitation->status->label() }}</flux:badge>
        @elseif ($invitation->isAccepted())
            <flux:heading size="lg" class="mb-2">Already Accepted</flux:heading>
            <p class="text-gray-600">This invitation has already been accepted.</p>
        @elseif ($invitation->isExpired())
            <flux:heading size="lg" class="mb-2">Invitation Expired</flux:heading>
            <p class="text-gray-600">This invitation has expired and is no longer valid.</p>
        @elseif ($invitation->isRevoked())
            <flux:heading size="lg" class="mb-2">Invitation Revoked</flux:heading>
            <p class="text-gray-600">This invitation has been revoked.</p>
        @elseif ($invitation->isDeclined())
            <flux:heading size="lg" class="mb-2">Invitation Declined</flux:heading>
            <p class="text-gray-600">This invitation has been declined.</p>
        @elseif ($invitation->isPending())
            <flux:heading size="lg" class="mb-2">You've Been Invited</flux:heading>
            <p class="text-gray-600 mb-6">{{ $invitation->name }}</p>

            <div class="flex justify-center gap-4">
                <flux:button wire:click="accept" variant="primary">Accept</flux:button>
                <flux:button wire:click="decline" variant="danger">Decline</flux:button>
            </div>
        @endif
    </flux:card>
</div>
