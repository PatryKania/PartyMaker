<?php

namespace App\Filament\EventPanel\Resources\Memories\Pages;

use App\Filament\EventPanel\Resources\Memories\MemoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMemories extends ListRecords
{
    protected static string $resource = MemoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
