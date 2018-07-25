<?php

namespace App\Http\Transformers;


use League\Fractal;

class ExchangeLogTransformer extends Fractal\TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'rates'                 => is_array($data->preference) ? $data['preference']['rates'] : json_decode($data['preference'])->rates, 
            'created_at'            =>  $data->created_at->toDateTimeString(),
            'updated_at'            =>  $data->updated_at->toDateTimeString(),
            'links'                 => [
                [
                    '_self'           => url("v1/exchanges/{$data->exchange_id}/logs/{$data->id}"),
                ]
            ],
        ];
    }
}