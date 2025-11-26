<?php

namespace App\Filament\EventPanel\Resources\Memories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

use Filament\Schemas\Components\Grid;

class MemoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Grid::make(2)
                    ->schema([
                        FileUpload::make('images')
                            ->image()
                            ->imageEditor()
                            ->multiple()->disk('public')
                            ->directory('memories/images')
                            ->visibility('public')
                            ->moveFiles(),

                        FileUpload::make('video')
                            ->directory('memories/videos')
                            ->visibility('public')
                            ->disk('public')
                            ->maxSize(10000)
                            ->acceptedFileTypes(['video/mp4']),

                        TextInput::make('desc')
                            ->required(),
                    ]),

            ]);
    }
}
