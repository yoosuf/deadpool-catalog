<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'preference' => 'array',
    ];
    
    protected $guarded = [];

    
}