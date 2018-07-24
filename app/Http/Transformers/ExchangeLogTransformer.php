<?php

namespace App\Http\Transformers;


use League\Fractal;

class ExchangeLogTransformer extends Fractal\TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id'                    =>  (int) $data->id,
            'preference'            =>  $data->preference,
            'created_at'            =>  $data->created_at->toDateTimeString(),
            'updated_at'            =>  $data->updated_at->toDateTimeString(),
        ];
    }
}