<?php

namespace App\Http\Transformers;


use League\Fractal;

class CountryTransformer extends Fractal\TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defualtIncludes = [
        'exchanges'
    ];


    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'exchanges'
    ];


    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'name'                  =>  $data->name,
            'nice_name'             =>  $data->nice_name,
            'iso'                   =>  $data->iso,
            'iso3'                  =>  $data->iso3,
            'phone_code'            =>  $data->phone_code,
            'preference'            =>  $data->preference,
            'created_at'            =>  $data->created_at->toDateTimeString(),
            'updated_at'            =>  $data->updated_at->toDateTimeString(),
            'links'                 => [
                [
                    '_self'           => url("v1/countries/{$data->id}"),
                ]
            ],
        ];
    }


    /**
     * Include Exchanges
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeExchanges($data)
    {
        $exchanges = $data->exchanges();
        return $this->collection($exchanges, new ExchangeTransformer);
    }
}