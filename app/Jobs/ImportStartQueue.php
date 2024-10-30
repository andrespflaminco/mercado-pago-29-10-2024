<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\DB;


class ImportStartQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = DB::table('jobs')->get();


        if($jobs !== null){
            foreach ($jobs as $job) {               
                if($job->attempts < 1  && $job->queue !== 'importStartQueue'){
                    Artisan::call('queue:work', [
                        //'--queue' => $job->queue,
                        '--once' => true,
                    ]);
                }                  
            }
        }
    }
}
