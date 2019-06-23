<?php


namespace RichardStyles\Pokemon\Models;


use Illuminate\Database\Eloquent\Model;
use RichardStyles\Pokemon\Facades\Pokemon as PokemonFacade;

class Pokemon extends Model
{
    const POKEAPI_ENDPOINT = 'pokemon';

    protected $fillable = [
        'name',
        'url'
    ];

    protected $appends = [
        'sprites'
    ];

    public function getDetail()
    {
        return PokemonFacade::detail($this);
    }

    public function cacheKey()
    {
        return 'pokemon.'.$this->name;
    }

    public function getIdAttribute($value)
    {
        $segments = collect(explode("/",$this->url));
        $segments->pop();
        return $segments->pop();
    }

    public function getSpritesAttribute($value)
    {
        if (empty($value)) {
            $spriteBaseUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/";
            return [
                "back_default" => $spriteBaseUrl."back/{$this->id}.png",
                "back_female" => null,
                "back_shiny" => $spriteBaseUrl."back/shiny/{$this->id}.png",
                "back_shiny_female" => null,
                "front_default" => $spriteBaseUrl."{$this->id}.png",
                "front_female" => null,
                "front_shiny" => $spriteBaseUrl."shiny/{$this->id}.png",
                "front_shiny_female" => null,
            ];
        }
        return $value;
    }
}
