<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use PHPUnit\Framework\Constraint\Count;


class Exchange extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'base_url' => $this->base_url,
            "cryptocurrencies" => $this->cryptoCurrencies($this),
            "options" => isset($this->preferences) ? $this->preferences : json_encode("{}"),
            "country" => new Country($this->country),
            "currency" => new Currency($this->currency),
            'is_active' => $this->is_active
        ];
    }


    public function cryptoCurrencies($data)
    {
        $crypts = $data->crypts;

        $currency_list = [];
        foreach ($crypts as $crypt) {

            $asking_rate = $crypt->pivot->asking_rate;
            $current_rate = $crypt->pivot->current_rate;
            $preferences = json_decode($crypt->pivot->preferences);
            
            $currency_list[$crypt->code] = [
                "api_url" => isset($preferences->api_url) ? $preferences->api_url : "",
                "api_version" => isset($preferences->api_version) ? $preferences->api_version : "",
                "fees" => isset($preferences->fees) ? $preferences->fees : "",
                "current_rate" => isset($current_rate) ? $current_rate : "0",
                "asking_rate" => isset($asking_rate) ? $asking_rate : "0",
                "updated_at" => $crypt->pivot->updated_at->toDateTimeString(),
                'is_active' => $crypt->pivot->is_active
            ];
        }

        if (!empty($currency_list)) {
            return $currency_list;
        } else {
            return json_decode("{}");
        }
    }
}