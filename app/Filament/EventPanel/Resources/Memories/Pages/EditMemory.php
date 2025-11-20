<?php

namespace App\Filament\EventPanel\Resources\Memories\Pages;

use App\Filament\EventPanel\Resources\Memories\MemoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMemory extends EditRecord
{
    protected static string $resource = MemoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
