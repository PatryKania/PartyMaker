<?php

namespace App\Filament\EventPanel\Resources\Gifts\Schemas;

use App\Services\AiSuggestionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Throwable;

class GiftForm
{
    private const AI_SUGGESTIONS_LIMIT = 10;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Gift'))
                    ->required()
                    ->live(onBlur: true),

                Section::make(__('AI gift suggestions'))
                    ->description(__('These are only suggestions. They are stored for this session. Copy one and paste it into the gift name.'))
                    ->schema([
                        Textarea::make('ai_context')
                            ->label(__('Short description for AI'))
                            ->placeholder(__('Optional short context'))
                            ->rows(2)
                            ->dehydrated(false)
                            ->live(onBlur: true),

                        Actions::make([
                            Action::make('suggestGiftsWithAi')
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

                                        $existingGiftNames = self::getExistingGiftNames($event);

                                        if (filled($get('name'))) {
                                            $existingGiftNames[] = trim((string) $get('name'));
                                        }

                                        $cachedNames = session()->get(
                                            self::aiSuggestionsSessionKey($event),
                                            []
                                        );

                                        if (is_array($cachedNames) && $cachedNames !== []) {
                                            $cachedNames = app(AiSuggestionService::class)->filterGiftNames(
                                                names: $cachedNames,
                                                existingNames: $existingGiftNames,
                                                limit: self::AI_SUGGESTIONS_LIMIT,
                                            );

                                            session()->put(
                                                self::aiSuggestionsSessionKey($event),
                                                $cachedNames
                                            );

                                            if ($cachedNames !== []) {
                                                $set('ai_suggestions', self::namesToRepeaterItems($cachedNames));

                                                Notification::make()
                                                    ->title(__('AI suggestions loaded'))
                                                    ->body(__('Suggestions were loaded from this session.'))
                                                    ->success()
                                                    ->send();

                                                return;
                                            }
                                        }

                                        $eventType = data_get($event, 'type.value', data_get($event, 'type'));

                                        $names = app(AiSuggestionService::class)->suggestGiftNames(
                                            context: [
                                                'event_type' => $eventType,
                                                'event_name' => $event->name ?? null,
                                                'user_short_description' => $get('ai_context'),
                                            ],
                                            existingNames: $existingGiftNames,
                                            limit: self::AI_SUGGESTIONS_LIMIT,
                                        );

                                        if ($names === []) {
                                            Notification::make()
                                                ->title(__('No new suggestions generated'))
                                                ->body(__('AI did not find new gift suggestions that are different from existing gifts.'))
                                                ->warning()
                                                ->send();

                                            return;
                                        }

                                        session()->put(
                                            self::aiSuggestionsSessionKey($event),
                                            $names
                                        );

                                        $set('ai_suggestions', self::namesToRepeaterItems($names));

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

                                    $names = session()->get(
                                        self::aiSuggestionsSessionKey($event),
                                        []
                                    );

                                    return ! is_array($names) || $names === [];
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

                                $cachedNames = session()->get(
                                    self::aiSuggestionsSessionKey($event),
                                    []
                                );

                                if (! is_array($cachedNames) || $cachedNames === []) {
                                    return [];
                                }

                                $existingGiftNames = self::getExistingGiftNames($event);

                                $names = app(AiSuggestionService::class)->filterGiftNames(
                                    names: $cachedNames,
                                    existingNames: $existingGiftNames,
                                    limit: self::AI_SUGGESTIONS_LIMIT,
                                );

                                session()->put(
                                    self::aiSuggestionsSessionKey($event),
                                    $names
                                );

                                return self::namesToRepeaterItems($names);
                            })
                            ->hidden(function (): bool {
                                $event = filament()->getTenant();

                                if (! $event) {
                                    return true;
                                }

                                $names = session()->get(
                                    self::aiSuggestionsSessionKey($event),
                                    []
                                );

                                return ! is_array($names) || $names === [];
                            })
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Suggestion'))
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->copyable(
                                        copyMessage: __('Copied to clipboard'),
                                        copyMessageDuration: 1500,
                                    )
                                    ->helperText(__('Click the copy button and paste this into the gift name.')),
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

    private static function getExistingGiftNames(object $event): array
    {
        if (! method_exists($event, 'gifts')) {
            return [];
        }

        return $event->gifts()
            ->pluck('name')
            ->filter()
            ->map(fn (string $name): string => trim($name))
            ->values()
            ->toArray();
    }

    private static function aiSuggestionsSessionKey(object $event): string
    {
        return 'event_' . $event->getKey() . '_gift_ai_suggestions';
    }

    private static function namesToRepeaterItems(array $names): array
    {
        return collect($names)
            ->map(fn (string $name): array => [
                'name' => $name,
            ])
            ->values()
            ->toArray();
    }
}