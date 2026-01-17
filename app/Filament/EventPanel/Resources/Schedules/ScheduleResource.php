<?php

namespace App\Filament\EventPanel\Resources\Schedules;

use App\Filament\EventPanel\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\EventPanel\Resources\Schedules\Pages\EditSchedule;
use App\Filament\EventPanel\Resources\Schedules\Pages\ListSchedules;
use App\Filament\EventPanel\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\EventPanel\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Resources\Forms\Components;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Schedule');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Schedule');
    }
}
