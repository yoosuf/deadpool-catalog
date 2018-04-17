<?php

namespace App\Transformers\Api\V1;

use League\Fractal\TransformerAbstract;

class CountryTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'nice_name' => $data->nice_name,
            'iso' => $data->iso,
            'iso3' => $data->iso3,
            'phone_code' => $data->phone_code,
            'is_active' => $data->is_active,
        ];
    }
}