<?php

namespace App\Transformers\Api\V1;

use League\Fractal\TransformerAbstract;

class CryptTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'code' => $data->code,
            'symbol' => $data->symbol,
            'is_active' => $data->is_active,
        ];
    }
}