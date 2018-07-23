<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $guarded = [];

    protected $casts = [
        'preference' => 'array',
        'created_at' => 'date', 
        'updated_at' => 'date', 

    ];

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function currency_values()
    {
        return $this->hasMany(CurrencyValue::class);
    }

}