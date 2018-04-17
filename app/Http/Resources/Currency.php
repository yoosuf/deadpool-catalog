<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Currency extends Resource
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
            'code' => $this->code,
            'symbol' => $this->symbol
        ];
    }
}