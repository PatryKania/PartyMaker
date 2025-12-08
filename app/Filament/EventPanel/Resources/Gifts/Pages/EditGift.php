<?php

namespace App\Filament\EventPanel\Resources\Gifts\Pages;

use App\Filament\EventPanel\Resources\Gifts\GiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGift extends EditRecord
{
    protected static string $resource = GiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
