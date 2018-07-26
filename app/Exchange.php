<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
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


    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function exchange_logs()
    {
        return $this->hasMany(ExchangeLog::class);
    }
}
