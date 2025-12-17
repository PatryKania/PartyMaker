<?php

namespace App\Filament\EventPanel\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

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
}
