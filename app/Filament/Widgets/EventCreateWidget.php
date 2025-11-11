<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class EventCreateWidget extends Widget
{
    protected string $view = 'filament.widgets.event-create';
    protected int | string | array $columnSpan = 'full';
}
