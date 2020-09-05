<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

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
        Str::macro('formatUrl',function($route){
            return str_replace('http://127.0.0.1:1234',' https://4djbivujdq.sharedwithexpose.com',route($route));
        }); 
    }
}
