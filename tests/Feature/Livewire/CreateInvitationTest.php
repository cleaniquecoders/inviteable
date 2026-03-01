<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Http\Livewire\CreateInvitation;
use CleaniqueCoders\Inviteable\Models\Invite;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

beforeEach(function () {
    Event::fake();
    Mail::fake();

    config(['inviteable.inviteable_types' => [
        'User' => User::class,
    ]]);

    // Register livewire routes manually for testing
    $routesPath = realpath(__DIR__.'/../../../routes/livewire.php');
    Route::prefix('invitations')
        ->middleware(['web'])
        ->group($routesPath);

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

it('renders the create component', function () {
    Livewire::test(CreateInvitation::class)
        ->assertOk();
});

it('validates required fields', function () {
    Livewire::test(CreateInvitation::class)
        ->call('save')
        ->assertHasErrors(['inviteableType', 'inviteableId', 'name']);
});

it('creates a single invitation', function () {
    Livewire::actingAs($this->user)
        ->test(CreateInvitation::class)
        ->set('inviteableType', User::class)
        ->set('inviteableId', (string) $this->user->id)
        ->set('name', 'New Invite')
        ->set('expiryHours', 24)
        ->call('save')
        ->assertRedirect(route('inviteable.dashboard'));

    expect(Invite::count())->toBe(1);
});

it('creates batch invitations', function () {
    Livewire::actingAs($this->user)
        ->test(CreateInvitation::class)
        ->set('inviteableType', User::class)
        ->set('inviteableId', (string) $this->user->id)
        ->set('batchMode', true)
        ->set('batchNames', "Alice\nBob\nCharlie")
        ->set('expiryHours', 24)
        ->call('save')
        ->assertRedirect(route('inviteable.dashboard'));

    expect(Invite::count())->toBe(3);
});

it('adds and removes metadata fields', function () {
    Livewire::test(CreateInvitation::class)
        ->call('addMetadataField')
        ->assertSet('metadataFields', [['key' => '', 'value' => '']])
        ->call('addMetadataField')
        ->assertCount('metadataFields', 2)
        ->call('removeMetadataField', 0)
        ->assertCount('metadataFields', 1);
});
