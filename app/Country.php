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

    protected $casts = [
        'preference' => 'array',
    ];

    public function currencies()
    {
        return $this->belongsToMany(Currency::class);
    }


    public function exchanges()
    {
        return $this->belongsToMany(Exchange::class);
    }
}