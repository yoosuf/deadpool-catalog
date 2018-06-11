<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $guarded = [];

    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }


    public function exchanges()
    {
        return $this->belongsToMany(Exchange::class);
    }
}