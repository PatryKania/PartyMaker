<?php

namespace App\Filament\EventPanel\Resources\Memories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

class MemoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('images')
                    ->image()
                    ->imageEditor(),
                FileUpload::make('videos'),
                TextInput::make('desc')->required(),

            ]);
    }
}
