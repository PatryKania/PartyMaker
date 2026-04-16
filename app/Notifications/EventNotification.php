<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;

class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {   
        view()->share('btnColor', $this->event->color ?? '#d97706');
        
        return (new MailMessage)
            ->subject(__('Reminder: Upcoming event ":event_name"', ['event_name' => $this->event->name]))
            ->greeting(__('Hello :user_name!', ['user_name' => $notifiable->name]))
            ->line(__('This is a friendly reminder that the event **:event_name** is scheduled for **:date**. We are excited to have you with us!', [
                'event_name' => $this->event->name,
                'date' => $this->event->date
            ]))
            ->action(__('View Event Details'), url('/events/' . $this->event->id))
            ->line(__('If you have any questions, please contact us. See you there!'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}