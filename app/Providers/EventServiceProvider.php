<?php

namespace App\Providers;

use App\Events\MyFirstEvent;
use App\Events\MySecondEvent;
use App\Listeners\MyFirstEvent\MyFirstEventListenOne;
use App\Listeners\MyFirstEvent\MyFirstEventListenTwo;
use App\Listeners\MySecondEvent\MySecondEventListenOne;
use App\Listeners\MySecondEvent\MySecondEventListenTwo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        MyFirstEvent::class => [
            MyFirstEventListenOne::class,
            MyFirstEventListenTwo::class
        ],

        MySecondEvent::class => [
            MySecondEventListenOne::class,
            MySecondEventListenTwo::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
