<?php

namespace App\Filament\EventPanel\Resources\Participants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label(__('First name'))->sortable()->searchable(),
                TextColumn::make('last_name')->label(__('Last name'))->sortable()->searchable(),
                TextColumn::make('type')->label(__('Type'))->sortable()->searchable(),
                TextColumn::make('role')->label(__('Role'))->sortable()->searchable(),
                TextColumn::make('status')->label(__('Status'))->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
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
