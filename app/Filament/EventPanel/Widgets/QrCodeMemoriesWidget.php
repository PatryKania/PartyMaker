<?php

namespace App\Filament\EventPanel\Widgets;

use Filament\Widgets\Widget;

class QrCodeMemoriesWidget extends Widget
{
    protected string $view = 'filament.event-panel.widgets.qr-code-memories-widget';

    protected int | string | array $columnSpan = 1;
}
