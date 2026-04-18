<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use NotificationChannels\Smsapi\SmsapiSMSMessage;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

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

        $channels = ['mail', 'database'];

        if ($notifiable->routeNotificationForSmsapi($this)) {
            $channels[] = 'smsapi';
        }

        return $channels;
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

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('Reminder: Upcoming event ":event_name"', ['event_name' => $this->event->name]))
            ->body(__('This is a friendly reminder that the event :event_name is scheduled for :date. We are excited to have you with us!', [
                'event_name' => $this->event->name,
                'date' => $this->event->date
            ]))
            ->info()
            ->getDatabaseMessage();
    }


    public function toSmsapi(object $notifiable): SmsapiSmsMessage
    {
        $message = new SmsapiSmsMessage();

        $phoneNumber = $notifiable->routeNotificationForSmsapi($this);

        $content = __('Reminder: Upcoming event :event_name is scheduled for :date.', [
            'event_name' => $this->event->name,
            'date' => $this->event->date
        ]);



        $message
            ->to($phoneNumber)
            ->content($content)
            ->normalize()
            ->test(config('services.smsapi.test_mode', true));

        Log::info("SMSAPI:", (array)$message->data);

        return $message;
    }
}
