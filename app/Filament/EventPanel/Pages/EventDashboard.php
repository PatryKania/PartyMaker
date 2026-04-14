<?php

namespace App\Filament\EventPanel\Pages;


use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\EventPanel\Widgets\QrCodeMemoriesWidget;
use App\Filament\EventPanel\Widgets\ParticipantStatsOverview;
use App\Filament\EventPanel\Widgets\MemoryStatsOverview;
use App\Filament\EventPanel\Widgets\QrCodeWidget;
use App\Filament\EventPanel\Widgets\QrCodeEventSiteWidget;
use App\Filament\EventPanel\Widgets\InvitationWidget;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;


class EventDashboard extends Dashboard
{
    protected static ?string $title = 'Event';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Event');
    }

    public function getTitle(): string
    {
        return Filament::getTenant()->name;
    }

    public function getColumns(): array|int
    {
        return 3;
    }

    public function getWidgets(): array
    {
        if (auth()->user()->hasPermissions())
            return [
                ParticipantStatsOverview::class,
                MemoryStatsOverview::class,
                QrCodeWidget::class,
                QrCodeMemoriesWidget::class,
                QrCodeEventSiteWidget::class,

            ];
        else {
            return [
                InvitationWidget::class,
            ];
        }
    }
}
