<?php

namespace App\Transformers\Api\V1;

use League\Fractal\TransformerAbstract;

class ExchangeTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'base_url' => $data->base_url,
            'is_active' => $data->is_active,
            "cryptocurrencies" => $this->cryptoCurrencies($data),
            "options" => $this->options($data)
        ];
    }

    public function cryptoCurrencies($data)
    {
        $crypts = $data->crypts;

        $currency_list = [];
        foreach ($crypts as $crypt) {
            $preferences = json_decode($crypt->pivot->preferences);
            $currency_list[$crypt->code] = [
                "api_url" => $preferences->api_url,
                "api_version" => $preferences->api_version,
                "fees" => $preferences->fees
            ];
        }

        if (!empty($currency_list)) {
            return $currency_list;
        } else {
            return json_decode("{}");
        }
    }
}