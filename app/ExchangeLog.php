<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
    
    // public function getDateFormat() {

    //     $carbon = new Carbon(); 
    
    //     return $carbon->format('d/m/Y');
    // }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}