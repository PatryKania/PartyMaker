<?php

namespace App\Filament\EventPanel\Resources\Participants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Actions\SendInvitationsBulkAction;
use App\Filament\Actions\SendInvitationAction;
use App\Filament\Actions\ConfirmInvitationAction;
use App\Filament\Actions\RejectInvitationAction;
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
                TextColumn::make('type')->label(__('Type'))->sortable()->searchable()->visible(auth()->user()->isOrganizer()),
                TextColumn::make('role')->label(__('Role'))->sortable()->searchable()->visible(auth()->user()->isOrganizer()),
                TextColumn::make('status')->label(__('Status'))->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                SendInvitationAction::make(),
                EditAction::make(),
                DeleteAction::make(),

                RejectInvitationAction::make(),
                ConfirmInvitationAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    SendInvitationsBulkAction::make(),
                ])->visible(auth()->user()->isOrganizer()),
            ]);
    }
}
