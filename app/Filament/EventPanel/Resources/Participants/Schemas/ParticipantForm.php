<?php

namespace App\Filament\EventPanel\Resources\Participants\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Enums\ParticipantRole;
use App\Enums\ParticipantType;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')->label(__('First name'))->required(),
                TextInput::make('last_name')->label(__('Last name'))->required(),
                TextInput::make('email')->label(__('E-mail'))->required()->email(),
                TextInput::make('phone')->label(__('Phone'))->tel()->prefix('+48')->mask('999-999-999'),
                Select::make('type')->label(__('Type'))
                    ->options(ParticipantType::class)->default('adult')->required()->native(false),
                Select::make('role')->label(__('Role'))
                    ->options(ParticipantRole::class)->default('guest')->required()->native(false),

            ]);
    }
}
