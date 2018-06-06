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
    protected $fillable = [
        'name',
        'nice_name',
        'iso',
        'iso3',
        'phonecode',
    ];
}