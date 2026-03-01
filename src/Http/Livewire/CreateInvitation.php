<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Livewire;

use CleaniqueCoders\Inviteable\InviteableManager;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateInvitation extends Component
{
    public string $inviteableType = '';

    public string $inviteableId = '';

    public string $name = '';

    public int $expiryHours = 48;

    public bool $batchMode = false;

    public string $batchNames = '';

    /** @var array<int, array{key: string, value: string}> */
    public array $metadataFields = [];

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        if ($this->batchMode) {
            return [
                'inviteableType' => ['required', 'string'],
                'inviteableId' => ['required', 'string'],
                'batchNames' => ['required', 'string'],
                'expiryHours' => ['required', 'integer', 'min:1'],
            ];
        }

        return [
            'inviteableType' => ['required', 'string'],
            'inviteableId' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'expiryHours' => ['required', 'integer', 'min:1'],
        ];
    }

    public function addMetadataField(): void
    {
        $this->metadataFields[] = ['key' => '', 'value' => ''];
    }

    public function removeMetadataField(int $index): void
    {
        unset($this->metadataFields[$index]);
        $this->metadataFields = array_values($this->metadataFields);
    }

    public function save(): void
    {
        $this->validate();

        $manager = app(InviteableManager::class);

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
        $modelClass = $this->inviteableType;
        $inviteable = $modelClass::findOrFail($this->inviteableId);

        $metadata = $this->buildMetadata();

        if ($this->batchMode) {
            $names = array_filter(array_map('trim', explode("\n", $this->batchNames)));
            $invitations = array_map(fn (string $name) => [
                'name' => $name,
                'metadata' => $metadata,
            ], $names);

            $manager->createBatch(
                inviteable: $inviteable,
                invitations: $invitations,
                invitedBy: auth()->id(),
            );

            session()->flash('message', count($names).' invitation(s) created successfully.');
        } else {
            $manager->create(
                inviteable: $inviteable,
                name: $this->name,
                invitedBy: auth()->id(),
                expiryHours: $this->expiryHours,
                metadata: $metadata,
            );

            session()->flash('message', 'Invitation created successfully.');
        }

        $this->reset(['name', 'batchNames', 'metadataFields']);

        $this->redirect(route('inviteable.dashboard'));
    }

    public function render(): View
    {
        return view('inviteable::livewire.create-invitation', [
            'inviteableTypes' => config('inviteable.inviteable_types', []),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildMetadata(): ?array
    {
        $metadata = [];
        foreach ($this->metadataFields as $field) {
            if ($field['key'] !== '') {
                $metadata[$field['key']] = $field['value'];
            }
        }

        return $metadata !== [] ? $metadata : null;
    }
}
