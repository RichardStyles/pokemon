<?php

namespace RichardStyles\Pokemon;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use RichardStyles\Pokemon\Extensions\CacheBridge;

class PokemonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // nothing to boot - package does not have any view, config etc
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('GuzzleHttp\Client', function () {
            return new Client;
        });
        // Register the main class to use with the facade
        $this->app->singleton('pokemon', function () {
            return new Pokemon($this->app->make('GuzzleHttp\Client'));
        });
    }
}
