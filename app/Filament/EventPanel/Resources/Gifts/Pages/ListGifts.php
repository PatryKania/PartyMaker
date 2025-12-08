<?php

namespace App\Filament\EventPanel\Resources\Gifts\Pages;

use App\Filament\EventPanel\Resources\Gifts\GiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGifts extends ListRecords
{
    protected static string $resource = GiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
