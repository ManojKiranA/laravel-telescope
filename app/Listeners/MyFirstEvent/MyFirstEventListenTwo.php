<?php

namespace App\Listeners\MyFirstEvent;

use Illuminate\Support\Str;
use App\Events\MyFirstEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MyFirstEventListenTwo implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MyFirstEvent  $event
     * @return void
     */
    public function handle(MyFirstEvent $event)
    {
        $start = now();
        
        User::query()
            ->inRandomOrder()
            ->take($event->recordsCount)
            ->get()
            ->each
            ->updateName();
        sleep(3);
        $end = now();

        logger()->info(
            Str::of('Queued Listener Started at ')
            ->prepend(' '.__CLASS__.' ')
            ->append($start->toDateTimeString())
            ->append(' and Ended at ')
            ->append($end->toDateTimeString())
            ->__toString()
        );

        
    }
}
