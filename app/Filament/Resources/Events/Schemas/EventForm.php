<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;



class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                DateTimePicker::make('date'),
                Select::make('status')
    ->options([
        'weeding' => 'Weeding',
        'birthday' => 'Birthday',
        'christening' => 'Christening',
    ])
            ]);
    }
}
