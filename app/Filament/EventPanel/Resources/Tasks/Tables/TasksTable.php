<?php

namespace App\Filament\EventPanel\Resources\Tasks\Tables;


use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('Name'))->sortable()->searchable(),
                TextColumn::make('due_date')->label(__('Date'))->sortable()->searchable(),
                TextColumn::make('participants')
                    ->label(__('Organizers'))
                    ->badge()
                    ->state(fn($record) => $record->participants->map(fn($p) => "{$p->first_name} {$p->last_name}")->toArray())
                    ->searchable(),
                IconColumn::make('is_completed')->label(__('Status'))->boolean()->sortable()->searchable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle_complete')
                    ->label(fn($record) => $record->is_completed ? __('Mark as incomplete') : __('Mark as completed'))
                    ->icon(fn($record) => $record->is_completed ? 'heroicon-o-x-mark' : 'heroicon-o-check')
                    ->color(fn($record) => $record->is_completed ? 'gray' : 'success')
                    ->action(fn($record) => $record->update(['is_completed' => !$record->is_completed])),
            ])
            ->toolbarActions([]);
    }
}
