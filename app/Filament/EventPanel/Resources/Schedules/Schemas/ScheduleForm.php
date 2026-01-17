<?php

namespace App\Filament\EventPanel\Resources\Schedules\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->maxLength(255),

                DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->native(false)
                    ->default(now()),

                TimePicker::make('start_time')
                    ->label('Początek')
                    ->required()
                    ->seconds(false),

                TimePicker::make('end_time')
                    ->label('Koniec')
                    ->seconds(false),

                Textarea::make('desc')
                    ->label('Opis')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
