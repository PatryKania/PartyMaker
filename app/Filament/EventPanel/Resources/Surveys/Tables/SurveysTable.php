<?php

namespace App\Filament\EventPanel\Resources\Surveys\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\VoteAction;

class SurveysTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->isOrganizer()) {
                    $query->where('is_active', true);
                }
            })
            ->columns([
                TextColumn::make('title')->label(__("Tytuł"))
                    ->searchable(),
                IconColumn::make('is_active')->label(__("Status"))
                    ->boolean(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                VoteAction::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
