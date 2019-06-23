<?php

namespace RichardStyles\Pokemon\Tests;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\TestCase;
use RichardStyles\Pokemon\Facades\Pokemon;
use RichardStyles\Pokemon\PokemonServiceProvider;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Psr7\Response;

class PokemonTest extends TestCaseWithApiCall
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = File::get(__DIR__.'/stubs/pokemon.json');
    }

    /** @test */
    public function a_pokemon_is_returned()
    {
        $pokemon = new \RichardStyles\Pokemon\Models\Pokemon([
            'name' => 'bulbasaur',
            'url' => 'https://pokeapi.co/api/v2/pokemon/1/'
        ]);

        Cache::shouldReceive('has')
            ->once()
            ->with($pokemon->cacheKey())->andReturn(false);

        Cache::shouldReceive('put')->andReturnTrue();

        $pokemon->getDetail();

        $this->assertJson($pokemon->toJson(), $this->stub);
    }

    /** @test */
    public function the_pokemon_detail_response_is_cached()
    {
        $pokemon = new \RichardStyles\Pokemon\Models\Pokemon([
            'name' => 'bulbasaur',
            'url' => 'https://pokeapi.co/api/v2/pokemon/1/'
        ]);

        Cache::shouldReceive('has')
            ->once()
            ->with($pokemon->cacheKey())->andReturn(false);

        Cache::shouldReceive('put')->andReturnTrue();
        // first call does not exist in cache
        $pokemon->getDetail();

        $this->assertJson($this->stub, $pokemon->toJson());

        Cache::shouldReceive('has')
            ->once()
            ->with($pokemon->cacheKey())->andReturn(true);
        Cache::shouldReceive('get')->once()->andReturn($this->stub);
        // this response is cached
        $pokemon->getDetail();

        $this->assertJson($this->stub, $pokemon->toJson());
    }

}
