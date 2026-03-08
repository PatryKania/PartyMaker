<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Enums\ParticipantStatus;

class RejectInvitationAction
{
    public static function make(): Action
    {
        return Action::make('reject')
            ->label(__('Reject'))
            ->color('gray')
            ->icon('heroicon-m-x-mark')
            ->requiresConfirmation()
            ->modalHeading(__('Reject invitation?'))
            ->modalDescription(__('Are you sure you want to reject this invitation?'))
            ->modalSubmitActionLabel(__('Yes, reject'))
            ->action(function (Participant $record) {
               $record->update(['status' => ParticipantStatus::Rejected]);
                Notification::make()
                    ->danger()
                    ->title(__('Invitation rejected'))
                    ->send();
            })->visible(fn ($record) =>(
             in_array($record->status,[ParticipantStatus::Pending,ParticipantStatus::Confirmed]) && $record->isReletedParticipant()));
    }
}