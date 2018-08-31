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

        $this->app->singleton('filesystem', function ($app) { return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem'); });
    
    }
}
