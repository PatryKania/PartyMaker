<?php

namespace App\Filament\EventPanel\Resources\Gifts\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Gift;

class GiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Gift'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->getStateUsing(function (Gift $record) {
                        if (!$record->reserved_by_id) return __('Free');
                        if ($record->reserved_by_id === Auth::id()) return __('Yours');
                        return __('Busy');
                    })
                    ->color(fn(string $state): string => match ($state) {
                        __('Free') => 'success',
                        __('Yours')  => 'info',
                        default => 'danger',
                    }),
            ])
            ->actions([
                Action::make('reserve')
                    ->label(__('Reserve'))
                    ->icon('heroicon-m-check')
                    ->color('primary')
                    ->action(fn(Gift $record) => $record->update(['reserved_by_id' => Auth::id()]))
                    ->visible(fn(Gift $record) => is_null($record->reserved_by_id)),

                Action::make('unreserve')
                    ->label(__('Slow down'))
                    ->color('gray')
                    ->icon('heroicon-m-x-mark')
                    ->action(fn(Gift $record) => $record->update(['reserved_by_id' => null]))
                    ->visible(fn(Gift $record) => $record->reserved_by_id === Auth::id()),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
