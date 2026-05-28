<?php

namespace App\Filament\EventPanel\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class VideoChat extends Page
{

    protected string $view = 'filament.event-panel.pages.video-chat';
    protected static ?string $slug = 'video-chat';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedVideoCamera;
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()->isOrganizer();
    }

    public static function getNavigationLabel(): string
    {
        return __('Conference Room');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Conference Room');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Conference Room');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('Conference Room'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('joinCall')
                ->label(__('Join call'))
                ->color('primary')
                ->button()
                ->extraAttributes([
                    'x-show' => '!($store.videoChat?.isInCall ?? false)',
                    'x-cloak' => true,
                ])
                ->alpineClickHandler('window.PartyMakerVideoChat?.startCall()'),

            Action::make('leaveCall')
                ->label(__('End call'))
                ->color('danger')
                ->button()
                ->extraAttributes([
                    'x-show' => '$store.videoChat?.isInCall ?? false',
                    'x-cloak' => true,
                ])
                ->alpineClickHandler('window.PartyMakerVideoChat?.leaveCall()'),
        ];
    }
}
