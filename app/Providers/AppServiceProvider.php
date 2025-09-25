<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (env('APP_ENV') === 'local') {
            URL::forceScheme('http');
        }
        else
        {
            URL::forceScheme('https');
        }
        config(['filesystems.disks.s3.url'    => array_key_exists('AWS_URL', $_SERVER) ? $_SERVER['AWS_URL'] : 'https://'. env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/']);
    }
}
