<?php

namespace App\Filament\EventPanel\Resources\EventPages\Pages;

use App\Filament\EventPanel\Resources\EventPages\EventPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventPage extends EditRecord
{
    protected static string $resource = EventPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
