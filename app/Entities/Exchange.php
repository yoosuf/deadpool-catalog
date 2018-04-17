<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'preferences' => 'array',
        'preferences.api' => 'array',
        'crypts.pivot.preferences' => 'array',
    ];


    public function crypts()
    {
        return $this->belongsToMany(Crypt::class, 'crypt_exchange',
            'exchange_id', 'crypt_id')
            ->withPivot('preferences', 'is_active', 'current_rate','asking_rate')
            ->withTimestamps();
    }


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    public function scopeCurrencyCode($query, $currencyCode)
    {
        if ($currencyCode) {
            return $query->whereHas('currency', function ($q) use ($currencyCode) {
                    return $q->where('currencies.code', '=', $currencyCode);
                });

        }

        return $query;

    }
}
