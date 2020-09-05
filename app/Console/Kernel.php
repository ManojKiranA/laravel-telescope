<?php

namespace App\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                ->everyMinute();

        $schedule->call(function () {
            $now = now();
            $string = Str::of('Log Generated at ')
                            ->append($now->toDateTimeString())
                            ->append(' on ')
                            ->append($now->getTimezone())
                            ->append(' timezone.');

            logger()->info($string->__toString());
        })
        ->name('LogCurrentTime')
        ->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
