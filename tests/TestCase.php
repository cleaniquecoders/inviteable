<?php

namespace CleaniqueCoders\Inviteable\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \CleaniqueCoders\Inviteable\InviteableServiceProvider::class,
        ];
    }
}
