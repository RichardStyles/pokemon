<?php

namespace RichardStyles\Pokemon\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RichardStyles\Pokemon\Facades\Pokemon;
use Illuminate\Support\Facades\File;

class AllPokemonTest extends TestCaseWithApiCall
{

    protected $allCached;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = File::get(__DIR__.'/stubs/all.json');
        $this->allCached = File::get(__DIR__.'/stubs/all-cached.json');
    }

    /** @test */
    public function all_returns_a_default_collection_of_20_pokemon()
    {
        Cache::shouldReceive('has')
            ->once()
            ->with('all.pokemon')->andReturn(false);

        Cache::shouldReceive('put')->andReturnTrue();

        $all = Pokemon::all();

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertEquals(20, $all->count());
        $this->assertJson($all->toJson(), $this->stub);
    }

    /** @test */
    public function the_pokemon_response_is_cached()
    {
        Cache::shouldReceive('has')
            ->once()
            ->with('all.pokemon')->andReturn(false);

        Cache::shouldReceive('put')->andReturnTrue();
        // first call does not exist in cache
        Pokemon::all();

        Cache::shouldReceive('has')
            ->once()
            ->with('all.pokemon')->andReturn(true);


        Cache::shouldReceive('get')->once()->andReturn($this->allCached );
        // this response is cached
        Pokemon::all();
    }

    /** @test */
    public function each_pokemon_has_sprite_attributes()
    {
        Cache::shouldReceive('has')
            ->once()
            ->with('all.pokemon')->andReturn(false);

        Cache::shouldReceive('put')->andReturnTrue();

        $all = Pokemon::all();
        $all->each(function(\RichardStyles\Pokemon\Models\Pokemon $pokemon){
            $spriteBaseUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/";
            $this->assertEquals([
                "back_default" => $spriteBaseUrl."back/{$pokemon->id}.png",
                "back_female" => null,
                "back_shiny" => $spriteBaseUrl."back/shiny/{$pokemon->id}.png",
                "back_shiny_female" => null,
                "front_default" => $spriteBaseUrl."{$pokemon->id}.png",
                "front_female" => null,
                "front_shiny" => $spriteBaseUrl."shiny/{$pokemon->id}.png",
                "front_shiny_female" => null,
            ], $pokemon->sprites);
        });

        Cache::shouldReceive('has')
            ->once()
            ->with('all.pokemon')->andReturn(true);
        Cache::shouldReceive('get')->once()->andReturn($this->allCached);
        // this response is cached
        $all = Pokemon::all();

        $all->each(function(\RichardStyles\Pokemon\Models\Pokemon $pokemon){
            $spriteBaseUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/";
            $this->assertEquals([
                "back_default" => $spriteBaseUrl."back/{$pokemon->id}.png",
                "back_female" => null,
                "back_shiny" => $spriteBaseUrl."back/shiny/{$pokemon->id}.png",
                "back_shiny_female" => null,
                "front_default" => $spriteBaseUrl."{$pokemon->id}.png",
                "front_female" => null,
                "front_shiny" => $spriteBaseUrl."shiny/{$pokemon->id}.png",
                "front_shiny_female" => null,
            ], $pokemon->sprites);
        });
    }
}
