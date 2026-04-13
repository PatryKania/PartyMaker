<?php
namespace App\Filament\EventPanel\Resources\EventPages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                    ->required()
                    ->live(onBlur: true) 
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),


                FileUpload::make('main_banner')->label(__('Main banner'))
                    ->image()
                              ->disk('public')
                        ->visibility('public')
                    ->imageEditor()
                    ->directory('eventPage/images'),

                Tabs::make('Content Tabs')
                    ->label(__('Main Content'))
                    ->tabs([
                        Tab::make('PL')
                            ->schema([
                                RichEditor::make('content.pl')
                                    ->label('Treść (PL)')
                                    ->fileAttachmentsDirectory('eventPage/attachments'),
                            ]),
                        Tab::make('EN')
                            ->schema([
                                RichEditor::make('content.en')
                                    ->label('Content (EN)')
                                    ->fileAttachmentsDirectory('eventPage/attachments'),
                            ]),
                    ]),

                Flex::make([
                    FileUpload::make('down_img')->label(__('Down image'))
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('eventPage/images'),

                    Tabs::make('Down Content Tabs')
                        ->label(__('Down Content'))
                        ->tabs([
                            Tab::make('PL')
                                ->schema([
                                    RichEditor::make('down_content.pl')
                                        ->label('Treść dolna (PL)'),
                                ]),
                            Tab::make('EN')
                                ->schema([
                                    RichEditor::make('down_content.en')
                                        ->label('Down Content (EN)'),
                                ]),
                        ]),
                ])->from('md'),

            ]);
    }
}