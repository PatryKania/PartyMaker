<?php

namespace App\Filament\EventPanel\Resources\Memories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

use Filament\Schemas\Components\Grid;
use PhpParser\Node\Stmt\Label;

class MemoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Grid::make(2)
                    ->schema([
                        FileUpload::make('images')->label(__('Images'))
                            ->image()
                            ->imageEditor()
                            ->multiple()->disk('public')
                            ->directory('memories/images')
                            ->visibility('public')
                            ->moveFiles(),

                        FileUpload::make('video')->label(__('Video'))
                            ->directory('memories/videos')
                            ->visibility('public')
                            ->disk('public')
                            ->maxSize(10000)
                            ->acceptedFileTypes(['video/mp4']),

                        TextInput::make('desc')->label(__('Description'))
                            ->required(),
                    ]),

            ]);
    }
}
