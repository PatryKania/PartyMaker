<?php

namespace App\Filament\EventPanel\Resources\EventPages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class EventPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('main_banner')->label(__('Main banner'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('eventPage/images')
                            ->visibility('public')
                            ->moveFiles(),

                RichEditor::make('content')->label(__('Content'))
                ->fileAttachmentsDirectory('eventPage/attachments'),
                TextInput::make('slug')->label(__('URL slug'))
                            ->required(),
            ]);
    }
}
