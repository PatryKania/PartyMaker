<?php

namespace App\Filament\EventPanel\Resources\EventPages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class EventPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)
            ->components([
                TextInput::make('slug')->label(__('URL slug'))
                    ->required()->unique()->live(onBlur: true) 
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->unique(),

                FileUpload::make('main_banner')->label(__('Main banner'))
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('eventPage/images')
                    ->visibility('public')
                    ->moveFiles(),

                RichEditor::make('content')->label(__('Content'))
                    ->fileAttachmentsDirectory('eventPage/attachments'),
                Flex::make([
                    FileUpload::make('down_img')->label(__('Down img'))
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('eventPage/images')
                        ->visibility('public')
                        ->moveFiles(),

                    RichEditor::make('down_content')->label(__('Down content'))
                        ->fileAttachmentsDirectory('eventPage/attachments'),
                ])->from('md')

            ]);
    }
}
