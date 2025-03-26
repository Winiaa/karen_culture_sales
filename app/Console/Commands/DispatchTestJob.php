<?php

namespace App\Console\Commands;

use App\Jobs\TestQueueJob;
use Illuminate\Console\Command;

class DispatchTestJob extends Command
{
    protected $signature = 'test:queue';
    protected $description = 'Dispatch a test job to the queue';

    public function handle()
    {
        TestQueueJob::dispatch();
        $this->info('Test job dispatched to the queue!');
    }
} 