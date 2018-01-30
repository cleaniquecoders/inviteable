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
