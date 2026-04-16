<?php

namespace App\Filament\EventPanel\Resources\Surveys\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
class SurveyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label(__("Title"))
                    ->required(),
                    
                Toggle::make('is_active')
                    ->label(__("Status"))
                    ->required(),

                Repeater::make('questions')
                    ->relationship()
                    ->label(__('Questions'))
                    ->addActionLabel(__('Add Question'))
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['question_text'] ?? __('New Question'))
                    ->schema([
                        TextInput::make('question_text')
                            ->label(__('Question'))
                            ->required()
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label(__('Type'))
                            ->options([
                                'radio' => __('Single Choice (Radio)'),
                                'checkbox' => __('Multiple Choice (Checkbox)'),
                            ])
                            ->required()
                            ->live(),

                        Repeater::make('options')
                            ->label(__('Answers'))
                            ->addActionLabel(__('Add Option'))
                            ->simple(
                                TextInput::make('option')->required()
                            )
                            ->visible(fn (Get $get) => in_array($get('type'), ['radio', 'checkbox']))
                            ->columnSpanFull(),
                    ]),
                    // ->columns(2),
            ]);
    }
}