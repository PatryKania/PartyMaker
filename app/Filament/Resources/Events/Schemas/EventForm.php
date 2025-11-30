<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;
use App\Enums\EventType;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Name'))->required()->unique(),
                DatePicker::make('date')->label(__('Date'))->required()->date()->afterOrEqual(today())->native(false),
                Select::make('type')->label(__('Type'))->required()
                    ->options(EventType::class)->native(false),
                ColorPicker::make('color')->label(__('Color'))
            ]);
    }
}
