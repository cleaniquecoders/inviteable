<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Flux\FluxServiceProvider;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(FluxServiceProvider::class);

        config([
            'inviteable.ui.enabled' => true,
            'inviteable.ui.layout' => 'layouts.app',
            'inviteable.routes.api' => true,
            'inviteable.inviteable_types' => [
                'User' => \Workbench\App\Models\User::class,
            ],
        ]);

        if (! $this->app->runningUnitTests()) {
            $packageRoot = realpath(__DIR__.'/../../../');
            $dbPath = $packageRoot.'/workbench/database/database.sqlite';

            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite' => [
                    'driver' => 'sqlite',
                    'database' => $dbPath,
                    'prefix' => '',
                    'foreign_key_constraints' => true,
                ],
            ]);
        }
    }

    public function boot(): void
    {
        $viewPaths = config('view.paths', []);
        array_unshift($viewPaths, __DIR__.'/../../resources/views');
        config(['view.paths' => $viewPaths]);
    }
}
