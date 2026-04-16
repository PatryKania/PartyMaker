<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Notifications\EventNotification;
use Illuminate\Support\Facades\Notification;
use App\Enums\ParticipantStatus;
class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send event reminders to participants 7 days and 1 day prior';

    public function handle()
    {
        $events = Event::whereDate('date', now()->addDays(7))
            ->orWhereDate('date', now()->addDays(1))
            ->with(['participants' => function ($query) {
                $query->where('status', ParticipantStatus::Confirmed); 
            }])
            ->get();

            // dd( $events);
        foreach ($events as $event) {
            if ($event->participants->isEmpty()) {
                continue;
            }

            Notification::send($event->participants, new EventNotification($event));
        }

        $this->info('Event reminders successfully queued!');
    }
}