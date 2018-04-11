<?php

namespace CleaniqueCoders\Inviteable;

use Illuminate\Support\ServiceProvider;

class InviteableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Migrations
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        /**
         * Routes
         */
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        /**
         * Views
         */
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'inviteable');
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/inviteables'),
        ], 'inviteable-views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
