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
        'created_at' => 'date', 
        'updated_at' => 'date', 

    ];
    
    protected function getDateFormat() {
        return Carbon::format('U');
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}