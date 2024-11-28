<?php

namespace Bishopm\Methodist\Facades;

use Illuminate\Support\Facades\Facade;

class Methodist extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'church';
    }
}
