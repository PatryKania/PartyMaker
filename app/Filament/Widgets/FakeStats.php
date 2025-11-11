<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FakeStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Registered users:', '487'),
            Stat::make('Events created', '39'),
            Stat::make('Positive reviews', '94%'),
        ];
    }
}
