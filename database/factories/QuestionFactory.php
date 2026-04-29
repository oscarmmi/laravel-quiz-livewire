<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'question_text' => $this->faker->sentence() . '?',
            'code_snippet' => null,
            'answer_explanation' => $this->faker->paragraph(),
            'more_info_link' => $this->faker->url(),
            'type' => \App\Enums\QuestionType::UniqueAnswer,
        ];
    }
}
