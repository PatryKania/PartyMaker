<?php

namespace App\Filament\EventPanel\Resources\Gifts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;


class GiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Gift'))
                    ->required(),
            ]);
    }
}
