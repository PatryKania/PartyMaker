<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendTaskReminders extends Command
{

    protected $signature = 'tasks:send-reminders';

    protected $description = 'Send notifications for tasks due in the next 24 hours';


    public function handle()
    {
        $startTime = now()->addDay(7)->startOfDay();
        $endTime = now()->addDay(7)->endOfDay();

        $tasks = Task::query()
            ->where('is_completed', false)
            ->whereBetween('due_date', [$startTime, $endTime])
            ->with('participants.user')
            ->get();
        foreach ($tasks as $task) {
            foreach ($task->participants as $participant) {
                if ($participant->user) {
                    $participant->user->notify(new TaskDueNotification($task));
                } elseif ($participant->email) {
                    Notification::route('mail', $participant->email)
                        ->notify(new TaskDueNotification($task));
                }
            }
        }

        $this->info("Sent reminders for {$tasks->count()} tasks.");
    }
}
