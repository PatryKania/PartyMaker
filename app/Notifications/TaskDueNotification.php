<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class TaskDueNotification extends Notification
{
    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Reminder: :title', ['title' => $this->task->title]))
            ->greeting(__('Hello!'))
            ->line(__('We are reminding you about the upcoming deadline for the task: :title', ['title' => $this->task->title]))
            ->line(__('Deadline: :date', ['date' => $this->task->due_date]))
            ->line(__('Good luck!'));
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('Task Reminder'))
            ->body(__('Task \':title\' is due on :date', [
                'title' => $this->task->title,
                'date' => $this->task->due_date
            ]))
            ->info()
            ->getDatabaseMessage();
    }
}
