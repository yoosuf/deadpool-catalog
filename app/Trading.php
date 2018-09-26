<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trading extends Model
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
        'start_response' => 'array',
        'end_response' => 'array'
    ];
}