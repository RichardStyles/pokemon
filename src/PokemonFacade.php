<?php

namespace RichardStyles\Pokemon;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RichardStyles\Pokemon\Pokemon
 */
class PokemonFacade extends Facade
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
