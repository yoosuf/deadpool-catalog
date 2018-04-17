<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'secret', 'permission',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'secret',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'preferences' => 'array',
    ];


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->secret = $model->generateNextApiToken();
        });
    }


    public static function generateNextApiToken()
    {
        $val = str_random(60);
        if (self::apiTokenExists($val)) {
            return self::generateNextApiToken();
        }
        return $val;
    }


    /**
     * @param $val
     * @return mixed
     */
    public static function apiTokenExists($val)
    {
        return self::whereSecret($val)->exists();
    }
}
