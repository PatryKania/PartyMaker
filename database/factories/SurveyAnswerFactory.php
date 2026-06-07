<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyAnswer>
 */
class SurveyAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'participant_id' => Participant::factory(),
            'survey_question_id' => SurveyQuestion::factory(),
            'value' => ['Tak'],
        ];
    }
}
