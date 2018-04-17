<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Crypt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $guarded = [];
     
    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = [];


    public function exchanges()
    {
        return $this->belongsToMany(Exchange::class, 'crypt_exchange',
            'crypt_id', 'exchange_id')->withPivot('preferences', 'is_active', 'current_rate','asking_rate');
    }

    
}
