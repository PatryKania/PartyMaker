<?php

namespace App\Filament\EventPanel\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class EventDashboard extends Page
{
    protected string $view = 'filament.event-panel.pages.event-dashboard';

    protected static ?string $title = 'Event';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public function getTitle(): string
    {
        return __('Event');
    }

    public static function getNavigationLabel(): string
    {
        return __('Event');
    }
}
