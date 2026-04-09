<?php

namespace App\Filament\EventPanel\Resources\EventPages\Pages;

use App\Filament\EventPanel\Resources\EventPages\EventPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\EventPage;

class ListEventPages extends ListRecords
{
    protected static string $resource = EventPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $record = EventPage::first();

        if ($record) {
            redirect(EventPageResource::getUrl('edit', ['record' => $record->id]));
        } else {
            redirect(EventPageResource::getUrl('create'));
        }
    }
}
