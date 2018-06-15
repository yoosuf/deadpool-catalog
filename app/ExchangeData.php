<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $guarded = [];

    public function exchanges()
    {
        return $this->belongsTo(Exchange::class);
    }

}