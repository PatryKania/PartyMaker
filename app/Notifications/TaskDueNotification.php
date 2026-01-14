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
        return method_exists($notifiable, 'getKey') ? ['mail', 'database'] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Przypomnienie: ' . $this->task->title)
            ->greeting('Witaj!')
            ->line('Przypominamy o zbliżającym się terminie zadania: ' . $this->task->title)
            ->line('Termin: ' . $this->task->due_date)
            ->line('Powodzenia!');
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Przypomnienie o zadaniu')
            ->body("Zadanie '{$this->task->title}' ma termin {$this->task->due_date}")
            ->warning()
            ->getDatabaseMessage();
    }
}
