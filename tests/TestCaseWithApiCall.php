<?php


namespace RichardStyles\Pokemon\Tests;


use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use RichardStyles\Pokemon\PokemonServiceProvider;

class TestCaseWithApiCall extends \Orchestra\Testbench\TestCase
{
    protected $stub;

    protected function getPackageProviders($app)
    {
        return [
            PokemonServiceProvider::class
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->stub = File::get(__DIR__.'/stubs/all.json');
        $mock = $this->mock(GuzzleHttp\Client::class);
        $mock->shouldReceive('get')
            ->andReturn(new Response(
                $status = 200,
                $headers = [],
                $this->stub
            ));
        $this->app->instance('GuzzleHttp\Client', $mock);
    }
}
