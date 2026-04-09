<?php

namespace App\Filament\EventPanel\Resources\EventPages\Pages;

use App\Filament\EventPanel\Resources\EventPages\EventPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventPage extends CreateRecord
{
    protected static string $resource = EventPageResource::class;

    protected static bool $canCreateAnother = false;
}
