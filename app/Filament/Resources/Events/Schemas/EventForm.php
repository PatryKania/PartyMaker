<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;


class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Name'))->required()->unique(),
                DatePicker::make('date')->label(__('Date'))->required()->date()->afterOrEqual(today())->native(false),
                Select::make('type')->label(__('Type'))->required()
                    ->options([
                        'wedding' => 'Wedding',
                        'birthday' => 'Birthday',
                        'christening' => 'Christening',
                    ])->native(false),
                ColorPicker::make('color')->label(__('Color'))
            ]);
    }
}
