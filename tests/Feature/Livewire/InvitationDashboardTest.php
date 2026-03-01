<?php

declare(strict_types=1);

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Http\Livewire\InvitationDashboard;
use CleaniqueCoders\Inviteable\Models\Invite;
use CleaniqueCoders\Inviteable\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

beforeEach(function () {
    Event::fake();
    Mail::fake();

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

it('renders the dashboard component', function () {
    Livewire::test(InvitationDashboard::class)
        ->assertOk();
});

it('displays invitations in table', function () {
    $this->user->invitations()->create([
        'name' => 'Dashboard Test',
        'token' => hash('sha256', 'dashboard-test'),
        'status' => InvitationStatus::Pending,
        'expired_at' => now()->addHours(24),
    ]);

    Livewire::test(InvitationDashboard::class)
        ->assertSee('Dashboard Test');
});

it('filters by search', function () {
    $this->user->invitations()->create([
        'name' => 'Alpha Invite',
        'token' => hash('sha256', 'alpha'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Beta Invite',
        'token' => hash('sha256', 'beta'),
        'status' => InvitationStatus::Pending,
    ]);

    Livewire::test(InvitationDashboard::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha Invite')
        ->assertDontSee('Beta Invite');
});

it('filters by status', function () {
    $this->user->invitations()->create([
        'name' => 'Pending One',
        'token' => hash('sha256', 'status-pending'),
        'status' => InvitationStatus::Pending,
    ]);

    $this->user->invitations()->create([
        'name' => 'Accepted One',
        'token' => hash('sha256', 'status-accepted'),
        'status' => InvitationStatus::Accepted,
        'accepted_at' => now(),
    ]);

    Livewire::test(InvitationDashboard::class)
        ->set('statusFilter', 'pending')
        ->assertSee('Pending One')
        ->assertDontSee('Accepted One');
});

it('sorts by column', function () {
    Livewire::test(InvitationDashboard::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'desc');
});

it('performs bulk delete', function () {
    $invite1 = $this->user->invitations()->create([
        'name' => 'Bulk 1',
        'token' => hash('sha256', 'bulk-1'),
        'status' => InvitationStatus::Pending,
    ]);

    $invite2 = $this->user->invitations()->create([
        'name' => 'Bulk 2',
        'token' => hash('sha256', 'bulk-2'),
        'status' => InvitationStatus::Pending,
    ]);

    Livewire::test(InvitationDashboard::class)
        ->set('selected', [$invite1->id, $invite2->id])
        ->call('bulkDelete');

    expect(Invite::count())->toBe(0);
});
