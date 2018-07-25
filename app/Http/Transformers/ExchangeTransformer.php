<?php

namespace App\Http\Transformers;


use League\Fractal;

class ExchangeTransformer extends Fractal\TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'name'                  =>  $data->name,
            'description'           =>  $data->description,
            'preference'            =>  $data->preference,
            'created_at'            =>  $data->created_at->toDateTimeString(),
            'updated_at'            =>  $data->updated_at->toDateTimeString(),
            'links'                 => [
                [
                    '_self'           => url("v1/exchanges/{$data->id}"),
                    'exchange_logs_uri'      => url("v1/exchanges/{$data->id}/logs")
                    
                ]
            ],
        ];
    }
}