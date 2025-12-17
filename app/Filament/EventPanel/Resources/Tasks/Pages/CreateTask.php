<?php

namespace App\Filament\EventPanel\Resources\Tasks\Pages;

use App\Filament\EventPanel\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
