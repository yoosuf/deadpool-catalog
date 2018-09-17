<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $guarded = [];

    // protected $fillable = [
    //     'name', 'email',
    // ];

    protected $casts = [
        'response' => 'array',
    ];
}