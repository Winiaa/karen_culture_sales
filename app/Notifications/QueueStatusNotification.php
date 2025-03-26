<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Queue\SerializesModels;

class QueueStatusNotification extends Notification
{
    use Queueable, SerializesModels;

    protected $failedJobs;
    protected $pendingJobs;
    protected $workerStatus;
    protected $memoryUsage;
    protected $errorMessage;

    public function __construct($failedJobs, $pendingJobs, $workerStatus, $memoryUsage = null, $errorMessage = null)
    {
        $this->failedJobs = $failedJobs;
        $this->pendingJobs = $pendingJobs;
        $this->workerStatus = $workerStatus;
        $this->memoryUsage = $memoryUsage;
        $this->errorMessage = $errorMessage;
    }

    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Queue Status Alert')
            ->line('Queue Status Report:')
            ->line("Failed Jobs: {$this->failedJobs}")
            ->line("Pending Jobs: {$this->pendingJobs}")
            ->line("Worker Status: {$this->workerStatus}");

        if ($this->memoryUsage) {
            $message->line("Memory Usage: {$this->memoryUsage}MB");
        }

        if ($this->errorMessage) {
            $message->line("Error: {$this->errorMessage}");
        }

        return $message;
    }

    public function toSlack($notifiable)
    {
        $message = (new SlackMessage)
            ->error()
            ->content('Queue Status Alert')
            ->attachment(function ($attachment) {
                $attachment
                    ->title('Queue Details')
                    ->fields([
                        'Failed Jobs' => $this->failedJobs,
                        'Pending Jobs' => $this->pendingJobs,
                        'Worker Status' => $this->workerStatus,
                    ]);

                if ($this->memoryUsage) {
                    $attachment->field('Memory Usage', "{$this->memoryUsage}MB");
                }

                if ($this->errorMessage) {
                    $attachment->field('Error', $this->errorMessage);
                }
            });

        return $message;
    }
} 