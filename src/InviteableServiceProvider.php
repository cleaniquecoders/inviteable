<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable;

use CleaniqueCoders\Inviteable\Console\ExpireInvitationsCommand;
use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Listeners\SendInvitationEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InviteableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('inviteable')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_invites_table');
    }

    public function packageBooted(): void
    {
        Event::listen(InvitationCreated::class, SendInvitationEmail::class);

        $this->configureRateLimiting();

        if ($this->app->runningInConsole()) {
            $this->commands([
                ExpireInvitationsCommand::class,
            ]);
        }

        if ($this->shouldRegisterApi()) {
            $this->registerApiRoutes();
        }

        if ($this->shouldRegisterUi()) {
            $this->registerLivewireComponents();
            $this->registerLivewireRoutes();
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(InviteableManager::class);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('inviteable', function (Request $request) {
            $maxAttempts = (int) config('inviteable.rate_limit.max_attempts', 10);
            $decayMinutes = (int) config('inviteable.rate_limit.decay_minutes', 1);

            return Limit::perMinutes($decayMinutes, $maxAttempts)
                ->by($request->ip() ?? 'unknown');
        });
    }

    private function shouldRegisterApi(): bool
    {
        return (bool) config('inviteable.routes.api', false);
    }

    private function registerApiRoutes(): void
    {
        Route::prefix(config('inviteable.routes.api_prefix', 'api/invitations'))
            ->middleware(config('inviteable.routes.api_middleware', ['api', 'auth:sanctum']))
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/api.php'));
    }

    private function shouldRegisterUi(): bool
    {
        return (bool) config('inviteable.ui.enabled', false);
    }

    private function registerLivewireComponents(): void
    {
        if (! class_exists(Livewire::class)) {
            return;
        }

        Livewire::component('inviteable-dashboard', Http\Livewire\InvitationDashboard::class);
        Livewire::component('inviteable-create', Http\Livewire\CreateInvitation::class);
        Livewire::component('inviteable-detail', Http\Livewire\InvitationDetail::class);
        Livewire::component('inviteable-accept-decline', Http\Livewire\AcceptDeclineInvitation::class);
        Livewire::component('inviteable-settings', Http\Livewire\InvitationSettings::class);
    }

    private function registerLivewireRoutes(): void
    {
        Route::prefix(config('inviteable.ui.prefix', 'invitations'))
            ->middleware(config('inviteable.ui.middleware', ['web', 'auth']))
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/livewire.php'));
    }
}
