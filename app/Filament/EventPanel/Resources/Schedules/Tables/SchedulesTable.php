<?php

namespace App\Filament\EventPanel\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable()->width('10%'),

                TextColumn::make('start_time')
                    ->label('Rozpoczęcie')
                    ->time('H:i')
                    ->sortable()->width('10%'),

                TextColumn::make('end_time')
                    ->label('Zakończenie')
                    ->time('H:i')
                    ->placeholder('-')->width('10%'),

                TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->wrap(),
            ])
            ->defaultSort('date', 'asc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
