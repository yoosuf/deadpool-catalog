<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Country extends Resource
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
            'nice_name' => $this->nice_name,
            'iso' => $this->iso,
            'iso3' => $this->iso3,
            'phone_code' => $this->phone_code,
        ];
    }
}