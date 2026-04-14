<?php

namespace App\Filament\EventPanel\Widgets;

use App\Models\MemoryMedia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemoryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $event = filament()->getTenant();
        $totalMemories = $event->memories()->count();

        $totalPhotos = MemoryMedia::whereHas('memory', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->where('type', 'image')->count();

        $totalVideos = MemoryMedia::whereHas('memory', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->where('type', 'video')->count();

        return [
            Stat::make(__('Total Memories'), $totalMemories)
                ->description(__('Shared moments'))
                ->descriptionIcon('heroicon-o-chat-bubble-bottom-center-text'),
                

            Stat::make(__('Total Photos'), $totalPhotos)
                ->description(__('Captured images'))
                ->descriptionIcon('heroicon-o-photo'),

            Stat::make(__('Total Videos'), $totalVideos)
                ->description(__('Recorded clips'))
                ->descriptionIcon('heroicon-o-video-camera'),
        ];
    }
}