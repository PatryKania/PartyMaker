<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FakeStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('Registered users:'), '487'),
            Stat::make(__('Events created'), '39'),
            Stat::make(__('Positive reviews'), '94%'),
        ];
    }
}
