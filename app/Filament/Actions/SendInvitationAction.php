<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use App\Models\Participant;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParticipantInvitation;
use App\Enums\ParticipantType;
use App\Enums\ParticipantStatus;
use Filament\Notifications\Notification;

class SendInvitationAction
{
    public static function make(): Action
    {
        return Action::make('sendInvitation')
            ->label(__('Send Invitation'))
            ->icon('heroicon-o-paper-airplane')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (Participant $record) => 
                $record->status === ParticipantStatus::New 
            )
            ->action(function (Participant $record) {
                 $record->update(['status' => ParticipantStatus::Pending]);
                if ($record->email && $record->status == ParticipantStatus::New) {
                        Mail::to($record->email)->queue(new ParticipantInvitation($record));
                }



                Notification::make()
                    ->title(__('Invitation sent!'))
                    ->success()
                    ->send();
            });
    }
}