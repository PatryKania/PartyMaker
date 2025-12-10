<?php

namespace App\Filament\EventPanel\Pages;

use App\Filament\EventPanel\Widgets\QrCodeMemoriesWidget;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\EventPanel\Widgets\QrCodeWidget;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;

class EventDashboard extends Dashboard
{
    protected static ?string $title = 'Event';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function getNavigationLabel(): string
    {
        return __('Event');
    }

    public function getTitle(): string
    {
        return Filament::getTenant()->name;
    }

    public function getWidgets(): array
    {
        return [
            QrCodeWidget::class,
            QrCodeMemoriesWidget::class
        ];
    }
}
