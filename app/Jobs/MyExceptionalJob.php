<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MyExceptionalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $number;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($number)
    {
        $this->queue = 'MyExceptionalJob';

        $this->number = $number;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logType = collect(['emergency','alert','critical','error','warning','notice','info','debug'])
                    ->shuffle()
                    ->first();

        if ($this->number % 2 !== 0) {
            throw new Exception("Number {$this->number} is Not Divisible By 2");
        };
        
        logger()->{$logType}($this->ordinal($this->number).' Exception logging');
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return [
            'JobClass:'.self::class
        ];
    }
    
    public function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
}
