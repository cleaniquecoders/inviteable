<?php

declare(strict_types=1);

arch('source files use strict types')
    ->expect('CleaniqueCoders\Inviteable')
    ->toUseStrictTypes();

arch('models extend Eloquent Model')
    ->expect('CleaniqueCoders\Inviteable\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch('enums are backed enums')
    ->expect('CleaniqueCoders\Inviteable\Enums')
    ->toBeEnums();

arch('concerns are traits')
    ->expect('CleaniqueCoders\Inviteable\Concerns')
    ->toBeTraits();

arch('exceptions extend Exception')
    ->expect('CleaniqueCoders\Inviteable\Exceptions')
    ->toExtend('Exception');
