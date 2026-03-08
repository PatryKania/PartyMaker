<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Enums\ParticipantStatus;

class ConfirmInvitationAction
{
    public static function make(): Action
    {
        return Action::make('confirm')
            ->label(__('Confirm'))
            ->icon('heroicon-m-check')
            ->color('success')
            ->action(function (Participant $record) {
               $record->update(['status' => ParticipantStatus::Confirmed]);
                Notification::make()
                    ->success()
                    ->title(__('Presence confirmed!'))
                    ->send();
            })->visible(fn ($record) =>(
            $record->status == ParticipantStatus::Pending && $record->isReletedParticipant()));

    }
}