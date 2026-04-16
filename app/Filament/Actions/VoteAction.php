<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\CheckboxList;

class VoteAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'vote';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (Survey $record): bool => (bool) $record->is_active)->label(function (Survey $record) {
                return $this->hasVoted($record) ? __('Your Vote') : __('Vote');
            })
            ->icon(function (Survey $record) {
                return $this->hasVoted($record) ? 'heroicon-m-check-circle' : 'heroicon-o-hand-raised';
            })
            ->color(function (Survey $record) {
                return $this->hasVoted($record) ? 'success' : 'primary';
            })
            ->modalHeading(fn (Survey $record) => $record->title)
            ->modalSubmitAction(function (Action $action, Survey $record) {
                return $this->hasVoted($record) ? false : $action->getModalSubmitAction();
            })
            ->fillForm(function (Survey $record) {
                if (!$this->hasVoted($record)) {
                    return [];
                }

                $answers = SurveyAnswer::where('participant_id', auth()->id())
                    ->whereHas('question', fn ($q) => $q->where('survey_id', $record->id))
                    ->get()
                    ->keyBy('survey_question_id');

                $data = [];
                foreach ($record->questions as $question) {
                    if (isset($answers[$question->id])) {
                        $data["question_{$question->id}"] = $answers[$question->id]->value;
                    }
                }
                return $data;
            })
            ->form(function (Survey $record) {
                $isVoted = $this->hasVoted($record);
                $fields = [];

                foreach ($record->questions as $question) {
                    $options = collect($question->options ?? [])
                        ->mapWithKeys(fn($opt) => [$opt => $opt])
                        ->toArray();

                    if ($question->type === 'text') {
                        $fields[] = TextInput::make("question_{$question->id}")
                            ->label($question->question_text)
                            ->required()
                            ->disabled($isVoted);
                    } elseif ($question->type === 'radio') {
                        $fields[] = Radio::make("question_{$question->id}")
                            ->label($question->question_text)
                            ->options($options)
                            ->required()
                            ->disabled($isVoted);
                    } elseif ($question->type === 'checkbox') {
                        $fields[] = CheckboxList::make("question_{$question->id}")
                            ->label($question->question_text)
                            ->options($options)
                            ->required()
                            ->disabled($isVoted);
                    }
                }
                
                return $fields;
            })
            ->action(function (array $data, Survey $record) {
                if ($this->hasVoted($record)) {
                    return;
                }

                foreach ($data as $key => $value) {
                    $questionId = str_replace('question_', '', $key);
                    SurveyAnswer::create([
                        'participant_id' => auth()->id(),
                        'survey_question_id' => $questionId,
                        'value' => $value,
                    ]);
                }
            });
    }

    private function hasVoted(Survey $record): bool
    {
        return SurveyAnswer::where('participant_id', auth()->id())
            ->whereHas('question', fn ($q) => $q->where('survey_id', $record->id))
            ->exists();
    }
}