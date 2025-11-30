<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))->sortable()->searchable(),
                TextColumn::make('date')->label(__('Date'))->sortable()->searchable()->date('d.m.Y'),
                TextColumn::make('type')->label(__('Typ'))->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn($record) => route('filament.event.pages.event-dashboard', ['tenant' => $record]))
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
