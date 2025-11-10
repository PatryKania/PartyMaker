<?php

namespace App\Filament\EventPanel\Resources\Participants\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('email')->required()->email(),
                TextInput::make('phone')->tel()->prefix('+48')->mask('999-999-999'),
                Select::make('type')
                    ->options([
                        'adult' => 'Adult',
                        'child' => 'Child',
                    ])->default('adult')->required()->native(false),
                // TextInput::make('event_id')->required()->default(fn() => app('currentEvent')->id)->hidden(),
            ]);
    }
}
