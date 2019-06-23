<?php

namespace RichardStyles\Pokemon;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use \RichardStyles\Pokemon\Models\Pokemon as PokemonModel;

class Pokemon
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Base URL for API
     */
    const BASE_URL = 'https://pokeapi.co/api/v2/';

    /**
     * Pokemon constructor.
     * @param  $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }


    /**
     * @param $class
     * @return mixed
     */
    public function call($class, $idOrName = '')
    {
        // send offset and limit so that all pokemon are returned from the API.
        // aware that if number of pokemon > 5000 then this number would need to be updated.
        $response = $this->client->get(static::BASE_URL.$class::POKEAPI_ENDPOINT.$idOrName, [
            'query' => [
                'offset' => 0,
                'limit' => 5000
            ]
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        if (Cache::has('pokemon')) {
            $cached = Cache::get('pokemon');
            $cachedDecoded = json_decode($cached, true);
            $results = $cachedDecoded['results'];
        }else {
            $data = $this->call(PokemonModel::class);
            $results = $data['results'];
        }
        $results = collect($results);

        $results = $results->map(function ($attributes) {
            return new PokemonModel($attributes);
        });
        Cache::put('pokemon', $results->toJson());

        return $results;
    }

    /**
     * @param $model Model
     * @return mixed
     */
    public function detail($model)
    {
        if (Cache::has($model->cacheKey())) {
            $attributes = Cache::get($model->cacheKey());
            return new PokemonModel(json_decode($attributes, true));
        }
        $attributes = $this->call(PokemonModel::class, '/'.$model->name);

        $model->forceFill($attributes);

        Cache::put($model->cacheKey(), $model->toJson());
        return $model;
    }
}
