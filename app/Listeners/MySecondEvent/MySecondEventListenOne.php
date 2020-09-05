<?php

namespace App\Listeners\MySecondEvent;

use Illuminate\Support\Str;
use App\Events\MySecondEvent;
use App\Tag;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MySecondEventListenOne implements ShouldQueue
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
     * @param  MySecondEvent  $event
     * @return void
     */
    public function handle(MySecondEvent $event)
    {
        $start = now();
        
        factory(Tag::class,$event->recordsCount)->create();
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
