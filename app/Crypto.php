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
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_active'
    ];
}