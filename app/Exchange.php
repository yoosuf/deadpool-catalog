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


    public function countries()
    {
        return $this->hasMany(Country::class);
    }

}