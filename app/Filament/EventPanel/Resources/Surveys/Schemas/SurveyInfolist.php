<?php

namespace App\Filament\EventPanel\Resources\Surveys\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class SurveyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label(__('Title'))
                    ->weight('bold'),

                IconEntry::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),

                RepeatableEntry::make('questions')
                    ->label(__('Questions & Statistics'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('question_text')
                            ->label(__('Question Text'))
                            ->weight('bold')
                            ->columnSpanFull(),

                        TextEntry::make('answer_stats')
                            ->label(__('Response Summary'))
                            ->html()
                            ->state(function (Model $record) {
                                $answers = $record->answers;

                                if ($answers->isEmpty()) {
                                    return '<span class="text-gray-500">' . __('No answers yet.') . '</span>';
                                }

                                $counts = [];
                                
                                foreach ($answers as $answer) {
                                    foreach ((array) $answer->value as $val) {
                                        if (!empty($val)) {
                                            $counts[$val] = ($counts[$val] ?? 0) + 1;
                                        }
                                    }
                                }

                                if (empty($counts)) {
                                    return '<span class="text-gray-500">' . __('No options selected.') . '</span>';
                                }

                                $html = '<ul class="list-disc list-inside space-y-1">';
                                foreach ($counts as $option => $count) {
                                    $html .= "<li>{$option}: <strong>{$count}</strong> " . __('votes') . "</li>";
                                }
                                
                                return $html . '</ul>';
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}