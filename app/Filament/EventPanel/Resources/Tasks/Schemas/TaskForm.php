<?php

namespace App\Filament\EventPanel\Resources\Tasks\Schemas;

use App\Models\Participant;
use App\Enums\ParticipantRole;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),

                DateTimePicker::make('due_date')
                    ->label(__('Due Date'))
                    ->required()
                    ->native(false),

                Select::make('participants')
                    ->label(__('Responsible Organizers'))
                    ->relationship(
                        name: 'participants',
                        titleAttribute: 'last_name',
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->where('role', ParticipantRole::Organizer)
                    )
                    ->getOptionLabelFromRecordUsing(fn(Participant $record) => "{$record->first_name} {$record->last_name}")
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->hidden(function () {
                        $event = filament()->getTenant();
                        return $event->participants()
                            ->where('role', ParticipantRole::Organizer)
                            ->count() <= 1;
                    })
                    ->default(function () {
                        $event = filament()->getTenant();
                        $organizers = $event->participants()
                            ->where('role', ParticipantRole::Organizer)
                            ->get();

                        return $organizers->count() === 1
                            ? [$organizers->first()->id]
                            : [];
                    }),

                Toggle::make('is_completed')
                    ->label(__('Completed'))
                    ->default(false),
            ]);
    }
}
