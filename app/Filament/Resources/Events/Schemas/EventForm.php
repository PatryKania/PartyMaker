<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;
use App\Enums\EventType;


class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Name'))->required()->unique(),
                DatePicker::make('date')->label(__('Date'))->required()->date()->afterOrEqual(today())->native(false),
                Select::make('type')->label(__('Type'))->required()
                    ->options(EventType::class)->native(false),
                ColorPicker::make('color')->label(__('Color')),
                RichEditor::make('invitation')->label(__('Invitation'))->helperText(__('Invitation sent to guests')),
                FileUpload::make('image')->label(__('Image'))
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('event/images')
                    ->visibility('public')
                    ->moveFiles()->helperText(__('Main event photo, visible on event page')),
                Checkbox::make('reminder_1')->label(__('Reminder 1 day before'))->columnSpanFull(),
                Checkbox::make('reminder_7')->label(__('Reminder 7 days before'))->columnSpanFull(),
                Checkbox::make('reminder_30')->label(__('Reminder 30 days before'))->columnSpanFull(),
            ]);
    }
}
