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
                    ->required()
                    ->datalist(function ($get) {
                  $event = filament()->getTenant();
                if (!$event) return [];

                    $suggestions = match ($event->type->value) {
                        'wedding' => [
                            'Photographer booking',
                            'Menu tasting',
                            'Sending invitations',
                            'Dress/Suit fitting',
                            'Wedding cake order',
                        ],
                        'birthday' => [
                            'Cake order',
                            'Balloon decorations',
                            'Playlist preparation',
                            'Guest list confirmation',
                        ],
                        'christening' => [
                            'Church date booking',
                            'Choosing godparents',
                            'Restaurant table booking',
                            'Purchase of christening set',
                        ],
                        'company_event' => [
                            'Meeting agenda preparation',
                            'AV equipment check',
                            'Catering order',
                            'Badge preparation',
                        ],
                        default => [],
                    };

                    return collect($suggestions)
                        ->map(fn($item) => __($item))
                        ->toArray();
                }),

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
