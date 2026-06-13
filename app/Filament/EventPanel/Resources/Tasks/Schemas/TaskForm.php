<?php

namespace App\Filament\EventPanel\Resources\Tasks\Schemas;

use App\Enums\ParticipantRole;
use App\Models\Participant;
use App\Services\AiSuggestionService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class TaskForm
{
    private const AI_SUGGESTIONS_LIMIT = 10;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required()
                    ->datalist(function () {
                        $event = filament()->getTenant();

                        if (! $event) {
                            return [];
                        }

                        $eventType = data_get($event, 'type.value', data_get($event, 'type'));

                        $suggestions = match ($eventType) {
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
                            ->map(fn (string $item): string => __($item))
                            ->toArray();
                    })
                    ->live(onBlur: true),

                DateTimePicker::make('due_date')
                    ->label(__('Due date'))
                    ->required()
                    ->native(false),

                Select::make('participants')
                    ->label(__('Responsible Organizers'))
                    ->relationship(
                        name: 'participants',
                        titleAttribute: 'last_name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('role', ParticipantRole::Organizer)
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Participant $record): string => "{$record->first_name} {$record->last_name}"
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->default(function (): array {
                        $event = filament()->getTenant();

                        if (! $event) {
                            return [];
                        }

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

                Section::make(__('AI suggestions'))
                    ->description(__('These are only suggestions. They are stored for this session. Copy one and paste it into the task title.'))
                    ->schema([
                        Textarea::make('ai_context')
                            ->label(__('Short description for AI'))
                            ->placeholder(__('Optional short context'))
                            ->rows(2)
                            ->dehydrated(false)
                            ->live(onBlur: true),

                        Actions::make([
                            Action::make('suggestTasksWithAi')
                                ->label(__('Suggest with AI'))
                                ->icon('heroicon-o-sparkles')
                                ->color('gray')
                                ->action(function (Get $get, Set $set): void {
                                    try {
                                        $event = filament()->getTenant();

                                        if (! $event) {
                                            Notification::make()
                                                ->title(__('No event selected'))
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        $existingTaskTitles = self::getExistingTaskTitles($event);

                                        if (filled($get('title'))) {
                                            $existingTaskTitles[] = trim((string) $get('title'));
                                        }

                                        $eventType = data_get($event, 'type.value', data_get($event, 'type'));

                                        $titles = app(AiSuggestionService::class)->suggestTaskTitles(
                                            context: [
                                                'event_type' => $eventType,
                                                'event_name' => $event->name ?? null,
                                                'user_short_description' => $get('ai_context'),
                                            ],
                                            existingTitles: $existingTaskTitles,
                                            limit: self::AI_SUGGESTIONS_LIMIT,
                                        );

                                        if ($titles === []) {
                                            Notification::make()
                                                ->title(__('No new suggestions generated'))
                                                ->body(__('AI did not find new task suggestions that are different from existing tasks.'))
                                                ->warning()
                                                ->send();

                                            return;
                                        }

                                        session()->put(
                                            self::aiSuggestionsSessionKey($event),
                                            $titles
                                        );

                                        $set('ai_suggestions', self::titlesToRepeaterItems($titles));

                                        Notification::make()
                                            ->title(__('AI suggestions generated'))
                                            ->body(__('Suggestions were saved for this session.'))
                                            ->success()
                                            ->send();
                                    } catch (Throwable $e) {
                                        report($e);

                                        Notification::make()
                                            ->title(__('AI suggestions failed'))
                                            ->body(__('Try again later.'))
                                            ->danger()
                                            ->send();
                                    }
                                }),

                            Action::make('clearAiSuggestions')
                                ->label(__('Clear suggestions'))
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->hidden(function (): bool {
                                    $event = filament()->getTenant();

                                    if (! $event) {
                                        return true;
                                    }

                                    $titles = session()->get(
                                        self::aiSuggestionsSessionKey($event),
                                        []
                                    );

                                    return ! is_array($titles) || $titles === [];
                                })
                                ->action(function (Set $set): void {
                                    $event = filament()->getTenant();

                                    if ($event) {
                                        session()->forget(self::aiSuggestionsSessionKey($event));
                                    }

                                    $set('ai_suggestions', []);

                                    Notification::make()
                                        ->title(__('AI suggestions cleared'))
                                        ->success()
                                        ->send();
                                }),
                        ]),

                        Repeater::make('ai_suggestions')
                            ->label(__('AI suggestions'))
                            ->dehydrated(false)
                            ->default(function (): array {
                                $event = filament()->getTenant();

                                if (! $event) {
                                    return [];
                                }

                                $cachedTitles = session()->get(
                                    self::aiSuggestionsSessionKey($event),
                                    []
                                );

                                if (! is_array($cachedTitles) || $cachedTitles === []) {
                                    return [];
                                }

                                $existingTaskTitles = self::getExistingTaskTitles($event);

                                $titles = app(AiSuggestionService::class)->filterTaskTitles(
                                    titles: $cachedTitles,
                                    existingTitles: $existingTaskTitles,
                                    limit: self::AI_SUGGESTIONS_LIMIT,
                                );

                                session()->put(
                                    self::aiSuggestionsSessionKey($event),
                                    $titles
                                );

                                return self::titlesToRepeaterItems($titles);
                            })
                            ->hidden(function (): bool {
                                $event = filament()->getTenant();

                                if (! $event) {
                                    return true;
                                }

                                $titles = session()->get(
                                    self::aiSuggestionsSessionKey($event),
                                    []
                                );

                                return ! is_array($titles) || $titles === [];
                            })
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('Suggestion'))
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->copyable(
                                        copyMessage: __('Copied to clipboard'),
                                        copyMessageDuration: 1500,
                                    )
                                    ->helperText(__('Click the copy button and paste this into the task title.')),
                            ])
                            ->columns(1)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->grid(2),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }

    private static function getExistingTaskTitles(object $event): array
    {
        if (! method_exists($event, 'tasks')) {
            return [];
        }

        return $event->tasks()
            ->pluck('title')
            ->filter()
            ->map(fn (string $title): string => trim($title))
            ->values()
            ->toArray();
    }

    private static function aiSuggestionsSessionKey(object $event): string
    {
        return 'event_' . $event->getKey() . '_task_ai_suggestions';
    }

    private static function titlesToRepeaterItems(array $titles): array
    {
        return collect($titles)
            ->map(fn (string $title): array => [
                'title' => $title,
            ])
            ->values()
            ->toArray();
    }
}