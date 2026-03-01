<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable;

use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Listeners\SendInvitationEmail;
use Illuminate\Support\Facades\Event;
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
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(InviteableManager::class);
    }
}
