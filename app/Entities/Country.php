<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
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


    protected $date = ['created_at', 'updated_at'];

    public function exchanges()
    {
        return $this->hasMany(Exchange::class);
    }
}
