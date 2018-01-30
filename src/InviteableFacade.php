<?php

namespace CleaniqueCoders\Inviteable;

/**
 * This file is part of inviteable
 *
 * @license MIT
 * @package inviteable
 */

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
