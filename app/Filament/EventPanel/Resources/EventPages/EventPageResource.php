<?php

namespace App\Filament\EventPanel\Resources\EventPages;

use App\Filament\EventPanel\Resources\EventPages\Pages\CreateEventPage;
use App\Filament\EventPanel\Resources\EventPages\Pages\EditEventPage;
use App\Filament\EventPanel\Resources\EventPages\Pages\ListEventPages;
use App\Filament\EventPanel\Resources\EventPages\Schemas\EventPageForm;
use App\Filament\EventPanel\Resources\EventPages\Tables\EventPagesTable;
use App\Models\EventPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventPageResource extends Resource
{
    protected static ?string $model = EventPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;
    
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'EventPage';

    public static function form(Schema $schema): Schema
    {
        return EventPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventPages::route('/'),
            'create' => CreateEventPage::route('/create'),
            'edit' => EditEventPage::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Event page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Event page');
    }
}
