<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckQueueStatus extends Command
{
    protected $signature = 'queue:status';
    protected $description = 'Check the status of the queue and jobs';

    public function handle()
    {
        // Get failed jobs count
        $failedJobs = DB::table('failed_jobs')->count();
        
        // Get pending jobs count
        $pendingJobs = DB::table('jobs')->count();
        
        // Get queue worker status
        $workerStatus = $this->getWorkerStatus();
        
        // Log the status
        Log::info('Queue Status', [
            'failed_jobs' => $failedJobs,
            'pending_jobs' => $pendingJobs,
            'worker_status' => $workerStatus
        ]);
        
        // Display status
        $this->info('Queue Status:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Failed Jobs', $failedJobs],
                ['Pending Jobs', $pendingJobs],
                ['Worker Status', $workerStatus],
            ]
        );
    }
    
    private function getWorkerStatus()
    {
        $pidFile = storage_path('logs/queue-worker.pid');
        if (!file_exists($pidFile)) {
            return 'Not Running';
        }
        
        $pid = file_get_contents($pidFile);
        if (posix_kill($pid, 0)) {
            return 'Running (PID: ' . $pid . ')';
        }
        
        return 'Not Running';
    }
} 