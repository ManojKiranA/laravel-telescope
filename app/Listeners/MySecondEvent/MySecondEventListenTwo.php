<?php

namespace App\Listeners\MySecondEvent;

use Illuminate\Support\Str;
use App\Events\MySecondEvent;
use App\Post;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MySecondEventListenTwo implements ShouldQueue
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
        
        Post::query()
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
