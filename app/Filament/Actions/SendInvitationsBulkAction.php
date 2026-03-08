<?php

namespace App\Filament\Actions;


use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParticipantInvitation;
use App\Enums\ParticipantType;
use Filament\Notifications\Notification;
use App\Enums\ParticipantStatus;

class SendInvitationsBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('sendInvitations')
            ->label(__('Send Invitations'))
            ->icon('heroicon-o-paper-airplane')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn () => auth()->user()->isOrganizer())
            ->action(function (Collection $records) {
                $sentCount = 0;

                foreach ($records as $record) {
                     $record->update(['status' => ParticipantStatus::Pending]);
                    if ($record->email && $record->status == ParticipantStatus::New) {
                        Mail::to($record->email)->queue(new ParticipantInvitation($record));
                        $sentCount++;
                    }
                }

                Notification::make()
                    ->title(__('Invitations sent successfully!'))
                    ->body(__(':count invitations have been added to the queue.', ['count' => $sentCount]))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
