<?php

namespace App\Filament\EventPanel\Resources\Guests\Pages;

use App\Filament\EventPanel\Resources\Guests\GuestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;
}
