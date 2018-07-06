<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\GraphQL\Type\TimestampType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton(TimestampType::class, function ($app) {
        //     return new TimestampType();
        // });
        //
    }
}
