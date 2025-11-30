<?php

namespace App\Filament\EventPanel\Resources\Memories;

use App\Filament\EventPanel\Resources\Memories\Pages\CreateMemory;
use App\Filament\EventPanel\Resources\Memories\Pages\EditMemory;
use App\Filament\EventPanel\Resources\Memories\Pages\ListMemories;
use App\Filament\EventPanel\Resources\Memories\Schemas\MemoryForm;
use App\Filament\EventPanel\Resources\Memories\Tables\MemoriesTable;
use App\Models\Memory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\EventPanel\Resources\Memories\Pages\ManageMemories;

class MemoryResource extends Resource
{
    protected static ?string $model = Memory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MemoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemoriesTable::configure($table);
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
            'index' => ManageMemories::route('/'),
            // 'index' => ListMemories::route('/'),
            'create' => CreateMemory::route('/create'),
            'edit' => EditMemory::route('/{record}/edit'),

        ];
    }

    public static function getModelLabel(): string
    {
        return __('Memory');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Memories');
    }
}
