<?php

namespace CleaniqueCoders\Inviteable;

use Illuminate\Support\ServiceProvider;

class InviteableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Configuration
         */
        $this->publishes([
            __DIR__ . '/config/inviteable.php' => config_path('inviteable.php'),
        ], 'inviteable');
        $this->mergeConfigFrom(
            __DIR__ . '/config/inviteable.php', 'inviteable'
        );

        /*
         * Migrations
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        /*
         * Routes
         */
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        /*
         * Views
         */
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'inviteable');
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/inviteable'),
        ], 'inviteable');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
