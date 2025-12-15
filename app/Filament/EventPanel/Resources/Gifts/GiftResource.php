<?php

namespace App\Filament\EventPanel\Resources\Gifts;

use App\Filament\EventPanel\Resources\Gifts\Pages\CreateGift;
use App\Filament\EventPanel\Resources\Gifts\Pages\EditGift;
use App\Filament\EventPanel\Resources\Gifts\Pages\ListGifts;
use App\Filament\EventPanel\Resources\Gifts\Schemas\GiftForm;
use App\Filament\EventPanel\Resources\Gifts\Tables\GiftsTable;
use App\Models\Gift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return GiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GiftsTable::configure($table);
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
            'index' => ListGifts::route('/'),
            'create' => CreateGift::route('/create'),
            'edit' => EditGift::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Gift');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Gifts');
    }
}
