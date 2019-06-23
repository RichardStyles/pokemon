<?php

namespace RichardStyles\Pokemon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RichardStyles\Pokemon\Pokemon
 */
class Pokemon extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pokemon';
    }
}
