<?php

namespace App\Mail;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $participant;

    /**
     * Create a new message instance.
     */
    public function __construct($participant)
    {
        $this->participant = $participant;
        $this->event = $participant->event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
          subject: __('Invitation for :event', ['event' => $this->event->name]),
        );
    }

    /**
     * Get the message content definition.
     */

    public function content(): Content
    {
        $invitation = $this->event->invitation;
        $content = $invitation?->content ?? [];
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        if (is_array($content)) {
            $content = $content[$locale]
                ?? $content[$fallbackLocale]
                ?? collect($content)->first()
                ?? '';
        }

        $guestName = trim(collect([
            $this->participant->first_name,
            $this->participant->last_name,
        ])->filter()->implode(' '));

        return new Content(
            view: 'emails.invitations.' . ($invitation?->theme ?? 'classic'),
            with: [
                'guestName' => $guestName ?: $this->participant->email,
                'eventName' => $this->event->name,
                'content' => $content,
                'primaryColor' => $this->event->color,
                'loginUrl' => Filament::getPanel('dashboard')->getLoginUrl()
            ],
        );
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
