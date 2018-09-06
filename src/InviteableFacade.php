<?php

namespace CleaniqueCoders\Inviteable;

use Illuminate\Support\Facades\Facade;

class InviteableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Inviteable';
    }
}
