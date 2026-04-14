<?php

namespace App\Filament\EventPanel\Widgets;

use Filament\Widgets\Widget;

class QrCodeEventSiteWidget extends Widget
{
     protected string $view = 'filament.event-panel.widgets.qr-code-event-site-widget';

    protected int | string | array $columnSpan = 1;
}
