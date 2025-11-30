<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class FakeUserStats extends ChartWidget
{
    protected ?string $heading = 'User type';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('User type'),
                    'data' => [457, 30],
                    'backgroundColor' => ['#36A2EB', '#b227b2ff'],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => [__('Guest'), __('Organizer')],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getHeading(): ?string
    {
        return __('User type');
    }
}
