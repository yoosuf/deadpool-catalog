<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeLog extends Model
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

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}