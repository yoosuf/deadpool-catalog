<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
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
}