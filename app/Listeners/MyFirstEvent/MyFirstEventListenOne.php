<?php

namespace App\Listeners\MyFirstEvent;

use App\Events\MyFirstEvent;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MyFirstEventListenOne
{
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

        factory(User::class,$event->recordsCount)->create();
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
