<?php

namespace App\Http\Transformers;


use League\Fractal;

class CryptoTransformer extends Fractal\TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'name'                  =>  $data->name,
            'code'                  =>  $data->code,
            'symbol'                =>  $data->symbol,
            'is_active'             =>  $data->is_active,
            'preference'            =>  $data->preference,
            'created_at'            =>  $data->created_at->toDateTimeString(),
            'updated_at'            =>  $data->updated_at->toDateTimeString(),
            'links'                 => [
                [
                    '_self'           => url("v1/cryptos/{$data->id}"),
                ]
            ],
        ];
    }
}