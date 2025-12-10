<?php

namespace App\Filament\EventPanel\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;

class QrCodeWidget extends Widget
{
    protected string $view = 'filament.event-panel.widgets.qr-code-widget';

    protected int | string | array $columnSpan = 1;

    // public static function canView(): bool
    // {
    //     return auth()->user()->isOrganizer();
    // }
}
