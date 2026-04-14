<?php

namespace App\Filament\EventPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParticipantStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $event = filament()->getTenant();

        $totalParticipants = $event->participants()->count();
        $confirmedParticipants = $event->participants()->where('status', 'confirmed')->count();
        $pendingParticipants = $event->participants()->where('status', 'pending')->count();

        return [
            Stat::make(__('Total Participants'), $totalParticipants)
                ->description(__('All people on the list'))
                ->descriptionIcon('heroicon-o-users'),

            Stat::make(__('Confirmed'), $confirmedParticipants)
                ->description(__('People who accepted invitation'))
                ->descriptionIcon('heroicon-o-check-circle'),

            Stat::make(__('Pending'), $pendingParticipants)
                ->description(__('Still waiting for response'))
                ->descriptionIcon('heroicon-o-clock'),
        ];
    }
}