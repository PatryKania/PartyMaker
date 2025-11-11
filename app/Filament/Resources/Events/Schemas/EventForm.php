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
                TextInput::make('name')->required()->unique(),
                DatePicker::make('date')->required()->date()->afterOrEqual(today())->native(false),
                Select::make('type')->required()
                    ->options([
                        'wedding' => 'Wedding',
                        'birthday' => 'Birthday',
                        'christening' => 'Christening',
                    ])->native(false),
                ColorPicker::make('color')
            ]);
    }
}
