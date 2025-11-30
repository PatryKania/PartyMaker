<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class FakeEventStats extends ChartWidget
{
    protected ?string $heading = 'Event type';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('Event type'),
                    'data' => [14, 20, 5],
                    'backgroundColor' => ['#36A2EB', '#b227b2ff', '#5ecfabff'],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => [__('Wedding'), __('Birthday'), __('Christening')],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getHeading(): ?string
    {
        return __('Event type');
    }
}
