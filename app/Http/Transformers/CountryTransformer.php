<?php

namespace App\Http\Transformers;


use League\Fractal;

class CountryTransformer extends Fractal\TransformerAbstract
{
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
                    'uri'           => 'countries/'.$data->id,
                ]
            ],
        ];
    }
}