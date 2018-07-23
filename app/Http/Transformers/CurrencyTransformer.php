<?php

namespace App\Http\Transformers;


use League\Fractal;

class CurrencyTransformer extends Fractal\TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'name'                  =>  $data->name,
            'iso'                   =>  $data->iso,
            'iso3'                  =>  $data->iso3,
            'symbol'                =>  $data->symbol,
            'phone_code'            =>  $data->phone_code,
            'preference'            =>  $data->preference,
            'created_at'            =>  $data->created_at->format('d-m-Y'),
            'updated_at'            =>  $data->updated_at->format('d-m-Y'),
            'links'                 => [
                [
                    'uri'           => 'currencies/'.$data->id,
                ]
            ],
        ];
    }
}