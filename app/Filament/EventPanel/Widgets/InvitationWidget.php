<?php

namespace App\Filament\EventPanel\Widgets;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class InvitationWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.event-panel.widgets.invitation-widget';

    protected int | string | array $columnSpan = 'full';

    public ?Event $event = null;

    public function mount(): void
    {
        $currentEvent = Filament::getTenant();

        if (!$currentEvent instanceof Event) {
            return;
        }

        $this->event = $currentEvent;
    }

    public function confirmAction(): Action
    {
        return Action::make('confirm')
            ->label(__('Confirm'))
            ->icon('heroicon-m-check')
            ->size('xl')
            ->action(function () {
                if (!$this->event) return;

                Auth::user()->events()->updateExistingPivot($this->event->id, [
                    'status' => 'confirmed',
                ]);

                Notification::make()
                    ->success()
                    ->title(__('Presence confirmed!'))
                    ->send();

                $this->redirect(request()->header('Referer'));
            });
    }

    public function rejectAction(): Action
    {
        return Action::make('reject')
            ->label(__('Reject'))
            ->color('gray')
            ->icon('heroicon-m-x-mark')
            ->size('xl')
            ->requiresConfirmation()
            ->modalHeading(__('Reject invitation?'))
            ->modalDescription(__('Are you sure you want to reject this invitation?'))
            ->modalSubmitActionLabel(__('Yes, reject'))
            ->action(function () {
                if (!$this->event) return;

                Auth::user()->events()->updateExistingPivot($this->event->id, [
                    'status' => 'rejected',
                ]);

                Notification::make()
                    ->danger()
                    ->title(__('Invitation rejected'))
                    ->send();

                $this->redirect("/dashboard/events");
            });
    }
}
